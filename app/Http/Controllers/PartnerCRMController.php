<?php

namespace App\Http\Controllers;

use App\Models\PartnerProspect;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PartnerCRMController extends Controller
{
    /**
     * Check if user is an approved partner.
     */
    protected function checkPartner()
    {
        if (!Auth::check() || !Auth::user()->isPartner()) {
            abort(403, 'Accès réservé aux partenaires approuvés.');
        }
    }

    /**
     * Display the CRM dashboard and prospects list.
     */
    public function index()
    {
        $this->checkPartner();

        $user = Auth::user();
        $prospects = $user->prospects()->orderBy('updated_at', 'desc')->get();

        // Group prospects by status
        $stages = [
            'new' => ['label' => 'Nouveau', 'color' => 'bg-blue-50 text-blue-800 border-blue-200', 'prospects' => collect()],
            'contacted' => ['label' => 'Contacté', 'color' => 'bg-yellow-50 text-yellow-800 border-yellow-200', 'prospects' => collect()],
            'interested' => ['label' => 'Intéressé', 'color' => 'bg-purple-50 text-purple-800 border-purple-200', 'prospects' => collect()],
            'proposal_sent' => ['label' => 'Devis envoyé', 'color' => 'bg-orange-50 text-orange-800 border-orange-200', 'prospects' => collect()],
            'negotiating' => ['label' => 'En négociation', 'color' => 'bg-indigo-50 text-indigo-800 border-indigo-200', 'prospects' => collect()],
            'won' => ['label' => 'Gagné', 'color' => 'bg-green-50 text-green-800 border-green-200', 'prospects' => collect()],
            'lost' => ['label' => 'Perdu', 'color' => 'bg-red-50 text-red-800 border-red-200', 'prospects' => collect()],
        ];

        foreach ($prospects as $prospect) {
            $status = $prospect->status;
            if (isset($stages[$status])) {
                $stages[$status]['prospects']->push($prospect);
            } else {
                $stages['new']['prospects']->push($prospect);
            }
        }

        // Summary stats
        $totalCA = $prospects->where('status', 'won')->sum('budget');
        $pipelineValue = $prospects->whereNotIn('status', ['won', 'lost'])->sum('budget');
        $prospectsCount = $prospects->count();
        $wonCount = $prospects->where('status', 'won')->count();

        // Get upcoming tasks/actions
        $upcomingActions = $user->prospects()
            ->whereNotNull('next_action_at')
            ->where('next_action_at', '>=', now())
            ->orderBy('next_action_at', 'asc')
            ->take(5)
            ->get();

        return view('pages.shop.partner.crm', compact('user', 'stages', 'totalCA', 'pipelineValue', 'prospectsCount', 'wonCount', 'upcomingActions'));
    }

    /**
     * Store a new prospect.
     */
    public function store(Request $request)
    {
        $this->checkPartner();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'need' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:new,contacted,interested,proposal_sent,negotiating,won,lost',
            'notes' => 'nullable|string|max:2000',
            'next_action_at' => 'nullable|date',
            'next_action_description' => 'nullable|string|max:255',
        ]);

        $validated['partner_id'] = Auth::id();

        PartnerProspect::create($validated);

        return redirect()->route('dashboard.partner.crm')->with('success', 'Prospect créé avec succès.');
    }

    /**
     * Update an existing prospect.
     */
    public function update(Request $request, PartnerProspect $prospect)
    {
        $this->checkPartner();

        if ($prospect->partner_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'company' => 'nullable|string|max:255',
            'need' => 'nullable|string|max:1000',
            'budget' => 'nullable|numeric|min:0',
            'status' => 'required|string|in:new,contacted,interested,proposal_sent,negotiating,won,lost',
            'notes' => 'nullable|string|max:2000',
            'next_action_at' => 'nullable|date',
            'next_action_description' => 'nullable|string|max:255',
        ]);

        $prospect->update($validated);

        return redirect()->route('dashboard.partner.crm')->with('success', 'Prospect mis à jour avec succès.');
    }

    /**
     * Delete a prospect.
     */
    public function destroy(PartnerProspect $prospect)
    {
        $this->checkPartner();

        if ($prospect->partner_id !== Auth::id()) {
            abort(403, 'Action non autorisée.');
        }

        $prospect->delete();

        return redirect()->route('dashboard.partner.crm')->with('success', 'Prospect supprimé avec succès.');
    }

    /**
     * Display the Assistant IA page.
     */
    public function assistantIndex()
    {
        $this->checkPartner();

        $user = Auth::user();
        $products = Product::where('active', true)->orderBy('name', 'asc')->get();

        return view('pages.shop.partner.assistant', compact('user', 'products'));
    }

    /**
     * Handle chat request with Gemini.
     */
    public function assistantChat(Request $request)
    {
        $this->checkPartner();

        $request->validate([
            'message' => 'required|string|max:2000',
            'product_id' => 'nullable|exists:products,id',
            'objective' => 'nullable|string|in:sell,inform,promote,discount,lead,objection,whatsapp_followup',
            'network' => 'nullable|string|in:facebook,instagram,linkedin,tiktok,whatsapp,email',
        ]);

        $apiKey = config('services.gemini.key');
        if (empty($apiKey)) {
            return response()->json([
                'success' => false,
                'message' => "La clé API Gemini n'est pas configurée dans l'application.",
            ], 500);
        }

        $model = config('services.gemini.model', 'gemini-1.5-flash');
        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$apiKey}";

        // Build the system instructions
        $systemInstruction = "Tu es l'assistant commercial IA privé d'un partenaire d'IT Holding.\n";
        $systemInstruction .= "Ton rôle est de l'aider à prospecter, vendre, fidéliser ses clients et créer du contenu marketing exceptionnel pour la promotion de la boutique IT Holding.\n";
        $systemInstruction .= "Sois persuasif, structuré, professionnel et utilise un ton chaleureux, adapté au public sénégalais.\n\n";

        if ($request->product_id) {
            $product = Product::find($request->product_id);
            $refCode = Auth::user()->partner_code;
            $refUrl = url('/partner/' . (Auth::user()->username ?: $refCode));
            
            $systemInstruction .= "PRODUIT À PROMOUVOIR :\n";
            $systemInstruction .= "- Nom : {$product->name}\n";
            $systemInstruction .= "- Prix : " . number_format($product->price, 0, ',', ' ') . " FCFA\n";
            if ($product->condition) {
                $systemInstruction .= "- État : " . ($product->condition === 'new' ? 'Neuf' : ($product->condition === 'refurbished' ? 'Reconditionné' : 'Venu d\'ailleurs')) . "\n";
            }
            $systemInstruction .= "- Lien d'affiliation du partenaire à inclure : {$refUrl}\n";
            $systemInstruction .= "- Description / Caractéristiques : " . strip_tags($product->description) . "\n\n";
        }

        if ($request->objective) {
            $objectives = [
                'sell' => "Rédiger un argumentaire de vente direct et convaincant, avec accroche forte et appel à l'action.",
                'inform' => "Donner des conseils informatifs ou astuces techniques liés au produit pour intéresser les clients.",
                'promote' => "Créer un message promotionnel mettant en valeur les bénéfices clés du produit.",
                'discount' => "Annoncer une offre spéciale ou une réduction (les clients ont -5% en utilisant le code promo du partenaire).",
                'lead' => "Créer un post pour susciter la curiosité et inviter le prospect à laisser ses coordonnées.",
                'objection' => "Aider à formuler une réponse commerciale à une objection client (ex: tarif trop cher, doute sur la garantie).",
                'whatsapp_followup' => "Rédiger un message de relance WhatsApp poli, amical et vendeur.",
            ];
            $systemInstruction .= "OBJECTIF DU MESSAGE : " . $objectives[$request->objective] . "\n\n";
        }

        if ($request->network) {
            $networks = [
                'facebook' => "Adapter le texte pour Facebook (longueur moyenne, espacé, utilisation d'emojis, accroche visible avant le bouton plus, hashtags en fin de post).",
                'instagram' => "Adapter le texte pour Instagram (très visuel, hashtags ciblés, phrases courtes, appel à l'action clair).",
                'linkedin' => "Adapter le texte pour LinkedIn (ton professionnel, axé B2B, valeur ajoutée pour les entreprises, structure nette).",
                'tiktok' => "Créer un script vidéo TikTok dynamique de 30-60 secondes (avec indications de scènes et voix off).",
                'whatsapp' => "Adapter pour WhatsApp (très court, direct, avec des listes à puces, des emojis pour attirer l'œil, appel à cliquer sur le lien ou répondre).",
                'email' => "Formater sous forme d'e-mail commercial bien rédigé avec un objet accrocheur, des formules de politesse et une signature.",
            ];
            $systemInstruction .= "FORMAT / CANAL : " . $networks[$request->network] . "\n\n";
        }

        $prompt = "Consigne demandée par le partenaire commercial :\n" . $request->message;

        try {
            $response = Http::timeout(60)->post($url, [
                'contents' => [
                    [
                        'parts' => [
                            ['text' => $systemInstruction . "\n" . $prompt]
                        ]
                    ]
                ]
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $reply = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($reply) {
                    return response()->json([
                        'success' => true,
                        'reply' => trim($reply),
                    ]);
                }
            }
            
            Log::error('Gemini API request failed for Partner Assistant: ' . $response->body());
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue lors de la communication avec le serveur IA.',
            ], 500);

        } catch (\Exception $e) {
            Log::error('Error calling Gemini API for Partner Assistant: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Impossible de joindre le service de génération IA.',
            ], 500);
        }
    }
}
