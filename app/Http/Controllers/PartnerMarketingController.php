<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\MarketingAsset;
use App\Models\PartnerScheduledPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Barryvdh\DomPDF\Facade\Pdf;

class PartnerMarketingController extends Controller
{
    /**
     * Check if user is approved partner.
     */
    protected function checkPartner()
    {
        if (!Auth::check() || !Auth::user()->isPartner()) {
            abort(403, 'Accès réservé aux partenaires approuvés.');
        }
    }

    /**
     * Display the marketing studio dashboard.
     */
    public function index()
    {
        $this->checkPartner();

        $user = Auth::user();
        $products = Product::with('images')->where('active', true)->orderBy('name', 'asc')->get();
        $assets = MarketingAsset::latest()->get();
        $scheduledPosts = $user->scheduledPosts()->orderBy('scheduled_at', 'asc')->get();

        // Group assets by category
        $groupedAssets = [
            'image' => $assets->where('category', 'image'),
            'pdf' => $assets->where('category', 'pdf'),
            'document' => $assets->where('category', 'document'),
            'template' => $assets->where('category', 'template'),
            'other' => $assets->where('category', 'other'),
        ];

        return view('pages.shop.partner.marketing', compact('user', 'products', 'groupedAssets', 'scheduledPosts'));
    }

    /**
     * Store a newly scheduled post.
     */
    public function storePost(Request $request)
    {
        $this->checkPartner();

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:2000',
            'platforms' => 'required|array|min:1',
            'platforms.*' => 'string|in:facebook,instagram,linkedin,whatsapp',
            'scheduled_at' => 'required|date',
            'product_id' => 'nullable|exists:products,id',
        ]);

        $validated['user_id'] = Auth::id();
        $validated['status'] = 'pending';

        PartnerScheduledPost::create($validated);

        return redirect()->route('dashboard.partner.marketing')->with('success', 'Publication programmée avec succès.');
    }

    /**
     * Publish scheduled post immediately.
     */
    public function publishPost(Request $request, PartnerScheduledPost $post)
    {
        $this->checkPartner();

        if ($post->user_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $post->update(['status' => 'published']);

        return redirect()->route('dashboard.partner.marketing')->with('success', 'La publication a été marquée comme publiée.');
    }

    /**
     * Delete a scheduled post.
     */
    public function destroyPost(PartnerScheduledPost $post)
    {
        $this->checkPartner();

        if ($post->user_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $post->delete();

        return redirect()->route('dashboard.partner.marketing')->with('success', 'Publication programmée supprimée.');
    }

    /**
     * Generate PDF Catalog for selected products.
     */
    public function generateCatalog(Request $request)
    {
        $this->checkPartner();

        $request->validate([
            'product_ids' => 'required|array|min:1',
            'product_ids.*' => 'exists:products,id',
            'catalog_title' => 'nullable|string|max:100',
        ]);

        $partner = Auth::user();
        $products = Product::whereIn('id', $request->product_ids)->get();
        $catalogTitle = $request->input('catalog_title') ?: 'Catalogue de produits recommandés';

        $pdf = Pdf::loadView('pages.shop.partner.pdf.catalog', compact('products', 'partner', 'catalogTitle'));

        return $pdf->download('catalogue_partenaire_' . ($partner->username ?: $partner->id) . '.pdf');
    }

    /**
     * Generate Video Script storyboard via Gemini.
     */
    public function generateVideoScript(Request $request)
    {
        $this->checkPartner();

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'tone' => 'required|string|in:friendly,energetic,corporate,urgent',
            'duration' => 'required|integer|in:15,30,60',
            'instructions' => 'nullable|string|max:1000',
        ]);

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => "La clé API Gemini n'est pas configurée dans l'application.",
            ], 500);
        }

        $product = Product::find($request->product_id);
        $partner = Auth::user();
        $refUrl = url('/partner/' . ($partner->username ?: $partner->partner_code));

        $toneLabels = [
            'friendly' => 'Amical, chaleureux et bienveillant (style recommandation proche)',
            'energetic' => 'Super dynamique, enthousiaste, captivant et rythmé',
            'corporate' => 'Professionnel, sérieux, structuré et rassurant',
            'urgent' => 'Pressant, axé opportunité rare, offre exclusive à ne pas rater',
        ];

        // Format prompt instructions
        $prompt = "Tu es un réalisateur de vidéos marketing courtes (Tiktok, Reels, Shorts) spécialisé dans le B2B et B2C.\n";
        $prompt .= "Génère un storyboard de vidéo promotionnelle de précisément 4 scènes pour le produit suivant.\n";
        $prompt .= "PRODUIT : {$product->name}\n";
        $prompt .= "PRIX : " . number_format($product->price, 0, ',', ' ') . " FCFA\n";
        $prompt .= "DESCRIPTION : " . strip_tags($product->description) . "\n";
        $prompt .= "TON DE LA VOIX : " . $toneLabels[$request->tone] . "\n";
        $prompt .= "DURÉE ESTIMÉE : {$request->duration} secondes au total.\n";
        $prompt .= "LIEN D'AFFILIATION DU PARTENAIRE À INJECTER DANS LE CTA DE LA SCÈNE 4 : {$refUrl}\n";
        if ($request->instructions) {
            $prompt .= "INSTRUCTIONS COMPLÉMENTAIRES : {$request->instructions}\n";
        }
        $prompt .= "\nPour chaque scène, fournis le visuel suggéré (ce que l'on voit à l'écran) et le script voix-off exact (ce que la voix-off dit en français).\n";
        $prompt .= "Format de réponse STRICTEMENT JSON. Ne mets aucun texte en dehors du JSON, pas de balises markdown de type ```json. Juste le JSON brut comme ceci :\n";
        $prompt .= "{\n";
        $prompt .= "  \"scenes\": [\n";
        $prompt .= "    {\n";
        $prompt .= "      \"num\": 1,\n";
        $prompt .= "      \"visual\": \"Description visuelle de la scène 1...\",\n";
        $prompt .= "      \"voiceover\": \"Texte dit par la voix-off de la scène 1...\"\n";
        $prompt .= "    },\n";
        $prompt .= "    ...\n";
        $prompt .= "  ]\n";
        $prompt .= "}\n";

        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(60)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($rawText) {
                    // Clean markdown wrapping if present
                    $cleaned = trim($rawText);
                    if (str_starts_with($cleaned, '```json')) {
                        $cleaned = substr($cleaned, 7);
                    }
                    if (str_ends_with($cleaned, '```')) {
                        $cleaned = substr($cleaned, 0, -3);
                    }
                    $cleaned = trim($cleaned);

                    $jsonDecoded = json_decode($cleaned, true);
                    if (json_last_error() === JSON_ERROR_NONE && isset($jsonDecoded['scenes'])) {
                        return response()->json([
                            'success' => true,
                            'scenes' => $jsonDecoded['scenes'],
                        ]);
                    }

                    // Fallback to text parsing or raw
                    Log::warning('Failed parsing Gemini JSON response for storyboard. Raw content: ' . $rawText);
                }
            }

            Log::error('Gemini API response failed for Video Creator: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => "La génération du script vidéo a échoué. Veuillez réessayer.",
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error calling Gemini for Video Creator: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Impossible de contacter l'assistant vidéo IA.",
            ], 500);
        }
    }

    /**
     * Generate AI Image Prompt via Gemini and return Pollinations rendering URL.
     */
    public function generatePosterAI(Request $request)
    {
        $this->checkPartner();

        $request->validate([
            'product_id' => 'required|exists:products,id',
            'theme' => 'nullable|string',
        ]);

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => "La clé API Gemini n'est pas configurée dans l'application.",
            ], 500);
        }

        $product = Product::find($request->product_id);
        
        $prompt = "You are an expert prompt engineer for AI image generators (like Imagen 3 or Midjourney).\n";
        $prompt .= "Generate a highly detailed, premium, and visually stunning text-to-image prompt (in English) to generate a professional marketing product shot or banner for the following IT equipment:\n";
        $prompt .= "PRODUCT: {$product->name}\n";
        $prompt .= "DESCRIPTION: " . strip_tags($product->description) . "\n\n";
        $prompt .= "Requirements:\n";
        $prompt .= "- Describe a realistic, clean, commercial product advertisement.\n";
        $prompt .= "- Include details about professional studio lighting, crisp details, high-end presentation, and modern tech backdrop.\n";
        $prompt .= "- Do NOT put text in the generated image itself.\n";
        $prompt .= "- Keep the prompt concise (40-60 words).\n";
        $prompt .= "- Reply ONLY with the final prompt, nothing else. Do not use quotes, intro or markdown code fences.";

        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        try {
            $response = Http::timeout(60)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $rawText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($rawText) {
                    $aiPrompt = trim($rawText);
                    $aiPrompt = trim($aiPrompt, '"\'');
                    $imageUrl = "https://image.pollinations.ai/prompt/" . rawurlencode($aiPrompt) . "?width=800&height=800&nologo=true&private=true";

                    return response()->json([
                        'success' => true,
                        'prompt' => $aiPrompt,
                        'image_url' => $imageUrl,
                    ]);
                }
            }

            Log::error('Gemini API response failed for Poster AI: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => "La génération du prompt par l'IA a échoué.",
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error calling Gemini for Poster AI: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => "Impossible de contacter l'IA de génération.",
            ], 500);
        }
    }

    /**
     * Proxy image requests to prevent CORS and Cloudflare blocks in headless browsers.
     */
    public function proxyImage(Request $request)
    {
        $this->checkPartner();

        $url = $request->query('url');
        if (empty($url)) {
            abort(400, 'Missing URL parameter');
        }

        // Validate host to prevent open proxy vulnerability
        $host = parse_url($url, PHP_URL_HOST);
        if ($host !== 'image.pollinations.ai') {
            abort(403, 'Unauthorized proxy host');
        }

        try {
            $response = Http::timeout(30)->get($url);
            if ($response->successful()) {
                return response($response->body())
                    ->header('Content-Type', $response->header('Content-Type') ?: 'image/jpeg')
                    ->header('Access-Control-Allow-Origin', '*')
                    ->header('Cache-Control', 'public, max-age=86400');
            }
            abort($response->status(), 'Failed to retrieve remote image');
        } catch (\Exception $e) {
            Log::error('Error proxying image: ' . $e->getMessage());
            abort(500, 'Internal Server Error');
        }
    }
}
