<?php

namespace App\Http\Controllers;

use App\Models\PartnerAiImage;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PartnerImageStudioController extends Controller
{
    private const RATIOS = [
        'square'    => 'square format 1:1',
        'portrait'  => 'vertical format 4:5 (Instagram / Facebook feed)',
        'story'     => 'tall vertical format 9:16 (story / WhatsApp status)',
        'landscape' => 'horizontal format 16:9 (banner)',
    ];

    public static function eligible(?User $user): bool
    {
        return $user
            && $user->isPartner()
            && in_array($user->partner_type, config('partner_ai.partner_types', []), true);
    }

    public static function usedToday(int $userId): int
    {
        return PartnerAiImage::where('user_id', $userId)
            ->where('created_at', '>=', now()->startOfDay())
            ->count();
    }

    public static function remaining(int $userId): int
    {
        return max(0, (int) config('partner_ai.daily_limit', 5) - self::usedToday($userId));
    }

    /** Chemins (disque public) des photos d'un produit, dans un ordre stable. */
    public static function productImagePaths(Product $product): array
    {
        return collect([$product->image])
            ->merge($product->images->pluck('path'))
            ->filter()
            ->unique()
            ->values()
            ->take(6)
            ->all();
    }

    public static function toArray(PartnerAiImage $image): array
    {
        return [
            'id'      => $image->id,
            'url'     => route('dashboard.partner.marketing.ai.show', $image->id),
            'type'    => $image->type,
            'prompt'  => $image->prompt,
            'created' => $image->created_at?->format('d/m H:i'),
        ];
    }

    private function studioUser(): User
    {
        $user = Auth::user();
        abort_unless(
            self::eligible($user),
            403,
            'Le Studio image IA est réservé aux revendeurs et aux partenaires commerciaux approuvés.'
        );

        return $user;
    }

    public function generate(Request $request)
    {
        $user = $this->studioUser();

        $data = $request->validate([
            'prompt' => ['required', 'string', 'min:5', 'max:800'],
            'ratio'  => ['required', 'in:' . implode(',', array_keys(self::RATIOS))],
        ], [
            'prompt.required' => 'Décrivez l’image souhaitée.',
            'prompt.min'      => 'Décrivez l’image souhaitée (au moins 5 caractères).',
        ]);

        $instruction = "Create a professional, realistic marketing image for IT Holding, a computer and IT equipment shop in Senegal.\n"
            . "Request from the partner: {$data['prompt']}\n"
            . 'Format: ' . self::RATIOS[$data['ratio']] . ".\n"
            . "Rules: do not write any text, price, logo, watermark or brand name inside the image. "
            . "Do not depict real identifiable people or celebrities. Keep it clean, commercial and suitable for social media.";

        return $this->run($user, 'generate', $data['prompt'], [['text' => $instruction]], null);
    }

    public function edit(Request $request)
    {
        $user = $this->studioUser();

        $data = $request->validate([
            'prompt'      => ['required', 'string', 'min:5', 'max:800'],
            'source'      => ['required', 'in:product,upload'],
            'product_id'  => ['required_if:source,product', 'nullable', 'exists:products,id'],
            'image_index' => ['nullable', 'integer', 'min:0', 'max:5'],
            'image'       => ['required_if:source,upload', 'nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ], [
            'prompt.required' => 'Indiquez la modification souhaitée.',
            'prompt.min'      => 'Indiquez la modification souhaitée (au moins 5 caractères).',
            'image.mimes'     => 'L’image doit être au format JPG, PNG ou WebP.',
            'image.max'       => 'L’image ne doit pas dépasser 4 Mo.',
            'image.required_if' => 'Choisissez une image à modifier.',
        ]);

        $productId = null;

        if ($data['source'] === 'product') {
            $product = Product::with('images')->where('active', true)->findOrFail($data['product_id']);
            $paths = self::productImagePaths($product);
            $path = $paths[(int) ($data['image_index'] ?? 0)] ?? null;

            if (!$path || !Storage::disk('public')->exists($path)) {
                return response()->json(['success' => false, 'message' => 'Photo du produit introuvable.'], 422);
            }

            $bytes = Storage::disk('public')->get($path);
            $mime = Storage::disk('public')->mimeType($path) ?: 'image/jpeg';
            $productId = $product->id;
        } else {
            $file = $request->file('image');
            $bytes = file_get_contents($file->getRealPath());
            $mime = $file->getMimeType() ?: 'image/jpeg';
        }

        if (!in_array($mime, ['image/jpeg', 'image/png', 'image/webp'], true)) {
            return response()->json(['success' => false, 'message' => 'Format d’image non pris en charge.'], 422);
        }

        $instruction = "Edit the provided image according to this request: {$data['prompt']}\n"
            . "Rules: keep the product itself faithful. Do not change its model, shape, ports, screen, colors or any feature unless the request explicitly says so; "
            . "change only the background, lighting, composition or what is requested. "
            . "Do not add any text, price, logo or watermark. Keep the result realistic and professional.";

        $parts = [
            ['text' => $instruction],
            ['inlineData' => ['mimeType' => $mime, 'data' => base64_encode($bytes)]],
        ];

        return $this->run($user, 'edit', $data['prompt'], $parts, $productId);
    }

    public function show(Request $request, PartnerAiImage $image)
    {
        $user = $this->studioUser();
        abort_unless($image->user_id === $user->id, 403);
        abort_unless(Storage::disk('local')->exists($image->path), 404);

        if ($request->boolean('download')) {
            return Storage::disk('local')->download($image->path, 'image-ia-' . $image->id . '.' . pathinfo($image->path, PATHINFO_EXTENSION));
        }

        return response()->file(Storage::disk('local')->path($image->path));
    }

    public function destroy(PartnerAiImage $image)
    {
        $user = $this->studioUser();
        abort_unless($image->user_id === $user->id, 403);

        Storage::disk('local')->delete($image->path);
        $image->delete();

        return response()->json(['success' => true, 'remaining' => self::remaining($user->id)]);
    }

    /** Vérifie le quota, appelle Gemini, enregistre l'image. */
    private function run(User $user, string $type, string $prompt, array $parts, ?int $productId)
    {
        if (self::remaining($user->id) <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'Vous avez atteint votre limite de ' . config('partner_ai.daily_limit') . ' images aujourd’hui. Revenez demain.',
            ], 429);
        }

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json(['success' => false, 'message' => 'Le service d’images IA n’est pas configuré.'], 500);
        }

        // Une seule création à la fois par partenaire (si le cache ne gère pas les verrous, on continue sans).
        $lock = null;
        try {
            $lock = Cache::lock('partner-ai-image:' . $user->id, 150);
            if (!$lock->get()) {
                return response()->json(['success' => false, 'message' => 'Une image est déjà en cours de création. Patientez un instant.'], 429);
            }
        } catch (\Throwable $e) {
            $lock = null;
        }

        try {
            set_time_limit(150);

            $model = config('partner_ai.model');
            $response = Http::timeout(120)
                ->withHeaders(['x-goog-api-key' => $apiKey])
                ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", [
                    'contents' => [['parts' => $parts]],
                    'generationConfig' => ['responseModalities' => ['TEXT', 'IMAGE']],
                ]);

            if (!$response->successful()) {
                Log::error('Gemini image API error', ['status' => $response->status(), 'body' => Str::limit($response->body(), 800)]);

                return response()->json([
                    'success' => false,
                    'message' => $response->status() === 429
                        ? 'Le service IA est très sollicité. Réessayez dans une minute.'
                        : 'La création de l’image a échoué. Réessayez dans un instant.',
                ], 502);
            }

            $json = $response->json();
            $b64 = null;
            $mime = 'image/png';

            foreach (($json['candidates'][0]['content']['parts'] ?? []) as $part) {
                $inline = $part['inlineData'] ?? $part['inline_data'] ?? null;
                if ($inline && !empty($inline['data'])) {
                    $b64 = $inline['data'];
                    $mime = $inline['mimeType'] ?? $inline['mime_type'] ?? 'image/png';
                    break;
                }
            }

            if (!$b64) {
                Log::warning('Gemini returned no image', ['feedback' => $json['promptFeedback'] ?? null, 'finish' => $json['candidates'][0]['finishReason'] ?? null]);

                return response()->json([
                    'success' => false,
                    'message' => 'L’IA n’a pas pu produire d’image pour cette demande. Reformulez-la avec d’autres mots.',
                ], 422);
            }

            $ext = match ($mime) {
                'image/jpeg' => 'jpg',
                'image/webp' => 'webp',
                default => 'png',
            };

            $path = "partner-ai/{$user->id}/" . Str::uuid() . '.' . $ext;
            Storage::disk('local')->put($path, base64_decode($b64));

            $image = PartnerAiImage::create([
                'user_id'    => $user->id,
                'type'       => $type,
                'prompt'     => $prompt,
                'product_id' => $productId,
                'path'       => $path,
            ]);

            return response()->json([
                'success'   => true,
                'image'     => self::toArray($image),
                'remaining' => self::remaining($user->id),
            ]);
        } catch (\Throwable $e) {
            Log::error('Partner AI image failure: ' . $e->getMessage());

            return response()->json(['success' => false, 'message' => 'Impossible de contacter l’IA. Réessayez.'], 500);
        } finally {
            optional($lock)->release();
        }
    }
}
