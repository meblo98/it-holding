<?php

namespace App\Services;

use App\Models\CrmDeal;
use App\Models\OrderItem;
use App\Models\PartnerCommission;
use App\Models\PartnerProspect;
use App\Models\Product;
use App\Models\Quote;
use App\Models\Service;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

/**
 * Assistant commercial IA (doc §44-45).
 *
 * Chaque action fournit à l'IA uniquement des données officielles calculées
 * ici. L'IA rédige et analyse, mais ne fixe jamais un prix, un stock, une
 * garantie, un taux ou une condition. Pour un devis, elle ne choisit que des
 * références du catalogue et des quantités ; les prix viennent de la base.
 * Le prix d'achat (purchase_price) n'est jamais transmis (doc §57).
 */
class SalesAssistant
{
    public const RULES = <<<'TXT'
Tu es l'assistant commercial interne d'IT Holding Services (Dakar, Sénégal), qui vend du matériel informatique, réseau, vidéosurveillance et des services IT.
Règles absolues :
- N'invente JAMAIS un prix, un stock, une garantie, un délai, un taux, une commission, une caractéristique technique ou une condition contractuelle.
- Utilise uniquement les données officielles fournies ci-dessous. Si une information manque, écris-le clairement (« à confirmer ») au lieu de la deviner.
- Montants en FCFA. Réponds en français, de façon concise et professionnelle, adaptée au contexte sénégalais.
TXT;

    /**
     * Relance client prête à envoyer (doc §44 : « Prépare une relance client »).
     */
    public function followUp(CrmDeal $deal, string $channel): string
    {
        $format = [
            'whatsapp' => 'Un message WhatsApp court et chaleureux (5 à 8 lignes maximum), sans objet.',
            'email' => "Un e-mail avec une ligne « Objet : … », un corps structuré et une formule de politesse. Signe « L'équipe commerciale IT Holding ».",
        ][$channel] ?? 'Un message court.';

        $prompt = "Rédige une relance pour cette affaire.\nFormat : {$format}\n"
            . "Objectif : faire avancer l'affaire vers l'étape suivante, sans pression excessive.\n\n"
            . $this->dealContext($deal);

        return $this->generate($prompt);
    }

    /**
     * Analyse d'une opportunité B2B (doc §44 : « Analyse cette opportunité B2B »).
     */
    public function analyzeDeal(CrmDeal $deal): string
    {
        $catalog = $this->relevantProducts($deal->need . ' ' . $deal->title);

        $prompt = "Analyse cette opportunité commerciale. Structure ta réponse en quatre parties courtes :\n"
            . "1. Résumé du besoin\n2. Offre possible avec nos produits (uniquement ceux listés, avec leurs données officielles)\n"
            . "3. Points à clarifier avec le client\n4. Prochaine action recommandée\n\n"
            . $this->dealContext($deal)
            . "\n\nPRODUITS DU CATALOGUE POTENTIELLEMENT PERTINENTS (données officielles) :\n"
            . ($catalog->isEmpty() ? "Aucun produit du catalogue ne correspond aux mots du besoin.\n" : $catalog->map(fn ($p) => '- ' . $this->productFacts($p))->implode("\n"));

        return $this->generate($prompt);
    }

    /**
     * Brouillon de devis à partir du besoin (doc §44 : « Génère un devis à partir de ce besoin »).
     * L'IA choisit des références du catalogue et des quantités ; les prix sont
     * ceux de la base. Ce qui ne correspond à rien est signalé, jamais chiffré.
     *
     * @return array{quote: ?Quote, unmatched: array<int, string>}
     */
    public function quoteFromNeed(CrmDeal $deal, User $author): array
    {
        if (blank($deal->need)) {
            throw new RuntimeException("Renseignez d'abord le besoin du client dans l'affaire.");
        }

        $products = Product::where('active', true)->orderBy('name')->limit(300)->get(['id', 'name', 'price', 'promo_price']);
        $services = Service::where('active', true)->orderBy('title')->limit(100)->get(['id', 'title', 'price']);

        $catalogLines = $products->map(fn ($p) => "P{$p->id} | {$p->name}")
            ->concat($services->map(fn ($s) => "S{$s->id} | {$s->title} (service)"))
            ->implode("\n");

        $prompt = "Transforme ce besoin client en lignes de devis en choisissant UNIQUEMENT des références du catalogue ci-dessous.\n"
            . "Réponds en JSON strict de la forme : {\"lines\":[{\"ref\":\"P12\",\"quantity\":2}],\"unmatched\":[\"besoin sans correspondance\"]}.\n"
            . "N'invente aucune référence. Ne mets pas de prix. Si une partie du besoin ne correspond à aucune référence, mets-la dans \"unmatched\".\n\n"
            . "BESOIN DU CLIENT :\n{$deal->need}\n\nCATALOGUE (référence | désignation) :\n{$catalogLines}";

        $result = $this->generate($prompt, json: true);

        $lines = [];
        foreach ((array) ($result['lines'] ?? []) as $line) {
            $ref = strtoupper(trim((string) ($line['ref'] ?? '')));
            $qty = max(1, (int) ($line['quantity'] ?? 1));

            if (preg_match('/^P(\d+)$/', $ref, $m) && ($p = $products->firstWhere('id', (int) $m[1]))) {
                $lines[] = ['description' => $p->name, 'quantity' => $qty, 'unit_price' => (float) $this->officialPrice($p)];
            } elseif (preg_match('/^S(\d+)$/', $ref, $m) && ($s = $services->firstWhere('id', (int) $m[1])) && $s->price) {
                $lines[] = ['description' => $s->title, 'quantity' => $qty, 'unit_price' => (float) $s->price];
            }
            // Référence inconnue : ignorée — l'IA ne peut pas faire entrer un produit ou un prix inventé.
        }

        $unmatched = array_values(array_filter(array_map('strval', (array) ($result['unmatched'] ?? []))));

        if (empty($lines)) {
            return ['quote' => null, 'unmatched' => $unmatched];
        }

        $quote = DB::transaction(function () use ($deal, $lines, $unmatched, $author) {
            $subtotal = collect($lines)->sum(fn ($l) => $l['quantity'] * $l['unit_price']);

            $notes = 'Brouillon préparé par l\'assistant IA à partir du besoin de l\'affaire ' . $deal->reference . ' — prix du catalogue, TVA et conditions à vérifier avant envoi.';
            if ($unmatched) {
                $notes .= "\nÀ chiffrer manuellement : " . implode(' ; ', $unmatched);
            }

            $quote = Quote::create([
                'number' => $this->nextQuoteNumber(),
                'user_id' => $author->id,
                'client_id' => $deal->client_id,
                'client_name' => $deal->display_name,
                'client_email' => $deal->contact_email ?: $deal->client?->email,
                'client_phone' => $deal->contact_phone ?: $deal->client?->phone,
                'subtotal' => $subtotal,
                'tax_amount' => 0,
                'total_amount' => $subtotal,
                'status' => 'draft',
                'valid_until' => now()->addDays(30)->toDateString(),
                'notes' => $notes,
                'share_token' => Str::random(32),
            ]);

            foreach ($lines as $l) {
                $quote->items()->create($l + ['total_price' => $l['quantity'] * $l['unit_price']]);
            }

            return $quote;
        });

        return ['quote' => $quote, 'unmatched' => $unmatched];
    }

    /**
     * Données de vente pour « Quels produits dois-je promouvoir cette semaine ? ».
     */
    public function promotionData(): array
    {
        $since = now()->subDays(30);

        $sales = OrderItem::query()
            ->join('orders', 'orders.id', '=', 'order_items.order_id')
            ->where('orders.status', '!=', 'cancelled')
            ->where('orders.created_at', '>=', $since)
            ->whereNotNull('order_items.product_id')
            ->groupBy('order_items.product_id')
            ->selectRaw('order_items.product_id, SUM(order_items.quantity) as qty')
            ->pluck('qty', 'product_id');

        $active = Product::where('active', true)->get();

        return [
            'best_sellers' => $active->filter(fn ($p) => ($sales[$p->id] ?? 0) > 0)
                ->sortByDesc(fn ($p) => $sales[$p->id])->take(8)
                ->map(fn ($p) => ['product' => $p, 'sold' => (int) $sales[$p->id]])->values(),
            'slow_movers' => $active->filter(fn ($p) => $p->stock >= 3 && !isset($sales[$p->id]))
                ->sortByDesc('stock')->take(8)->values(),
            'on_promo' => $active->filter(fn ($p) => $p->promo_price > 0 && $p->promo_price < $p->price)->take(8)->values(),
        ];
    }

    public function promotionAdvice(array $data): string
    {
        $section = fn (string $title, Collection $items, callable $line) => $title . " :\n" . ($items->isEmpty() ? "(aucun)\n" : $items->map($line)->implode("\n") . "\n");

        $prompt = "Recommande 3 à 5 produits à mettre en avant cette semaine auprès des clients et des partenaires, avec pour chacun la raison et un angle de communication (une phrase).\n"
            . "Priorise l'écoulement du stock dormant et les promotions en cours, sans négliger les meilleures ventes.\n\n"
            . $section('MEILLEURES VENTES (30 derniers jours)', $data['best_sellers'], fn ($r) => '- ' . $this->productFacts($r['product']) . " ; vendus : {$r['sold']}")
            . $section('STOCK DORMANT (aucune vente depuis 30 jours)', $data['slow_movers'], fn ($p) => '- ' . $this->productFacts($p))
            . $section('PROMOTIONS EN COURS', $data['on_promo'], fn ($p) => '- ' . $this->productFacts($p));

        return $this->generate($prompt);
    }

    /**
     * Classement déterministe des partenaires (doc §44 : « Quel partenaire génère le plus de ventes ? »).
     */
    public function partnerRanking(int $days = 90): Collection
    {
        $since = now()->subDays($days);

        $sales = PartnerCommission::where('created_at', '>=', $since)
            ->where('status', '!=', 'cancelled')
            ->groupBy('partner_id')
            ->selectRaw('partner_id, COUNT(*) as orders, SUM(order_amount) as revenue, SUM(commission_amount) as commissions')
            ->get()->keyBy('partner_id');

        $deals = PartnerProspect::opportunities()->where('status', 'won')->where('updated_at', '>=', $since)
            ->groupBy('partner_id')
            ->selectRaw('partner_id, COUNT(*) as won, SUM(contract_amount) as amount')
            ->get()->keyBy('partner_id');

        $ids = $sales->keys()->merge($deals->keys())->unique();

        return User::whereIn('id', $ids)->get()->map(fn (User $u) => [
            'user' => $u,
            'orders' => (int) ($sales[$u->id]->orders ?? 0),
            'revenue' => (float) ($sales[$u->id]->revenue ?? 0),
            'commissions' => (float) ($sales[$u->id]->commissions ?? 0),
            'deals_won' => (int) ($deals[$u->id]->won ?? 0),
            'deals_amount' => (float) ($deals[$u->id]->amount ?? 0),
        ])->sortByDesc(fn ($r) => $r['revenue'] + $r['deals_amount'])->values();
    }

    public function partnerAdvice(Collection $ranking, int $days): string
    {
        $rows = $ranking->take(15)->map(fn ($r) => "- {$r['user']->name} ({$r['user']->partner_type_label}) : {$r['orders']} commande(s), CA généré "
            . number_format($r['revenue'], 0, ',', ' ') . " FCFA, commissions " . number_format($r['commissions'], 0, ',', ' ')
            . " FCFA, {$r['deals_won']} opportunité(s) gagnée(s) pour " . number_format($r['deals_amount'], 0, ',', ' ') . ' FCFA')->implode("\n");

        $prompt = "Voici l'activité des partenaires sur les {$days} derniers jours. Identifie les meilleurs contributeurs, "
            . "les partenaires à relancer ou à accompagner, et propose 3 actions concrètes pour développer le réseau commercial.\n\n"
            . ($rows ?: 'Aucune activité partenaire sur la période.');

        return $this->generate($prompt);
    }

    /**
     * Faits officiels d'un produit, sans prix d'achat.
     */
    public function productFacts(Product $p): string
    {
        $price = number_format($this->officialPrice($p), 0, ',', ' ') . ' FCFA';
        if ($p->promo_price > 0 && $p->promo_price < $p->price) {
            $price .= ' (promo, prix normal ' . number_format($p->price, 0, ',', ' ') . ' FCFA)';
        }

        $stock = $p->stock > 0 ? "en stock ({$p->stock})" : ($p->isPreorderable() ? 'précommande, disponible le ' . $p->available_at->format('d/m/Y') : 'rupture de stock');
        $warranty = $p->warranty_duration_months ? "garantie {$p->warranty_duration_months} mois" : 'garantie : à confirmer';

        return "{$p->name} — {$price} — {$stock} — {$warranty}";
    }

    private function officialPrice(Product $p): float
    {
        return (float) (($p->promo_price > 0 && $p->promo_price < $p->price) ? $p->promo_price : $p->price);
    }

    private function relevantProducts(string $text): Collection
    {
        $words = collect(preg_split('/[^\p{L}\p{N}]+/u', Str::lower($text)))
            ->filter(fn ($w) => mb_strlen($w) >= 4)->unique()->take(12);

        if ($words->isEmpty()) {
            return collect();
        }

        return Product::where('active', true)
            ->where(fn ($q) => $words->each(fn ($w) => $q->orWhere('name', 'like', "%{$w}%")))
            ->limit(15)->get();
    }

    private function dealContext(CrmDeal $deal): string
    {
        $deal->loadMissing(['client', 'quote.items', 'activities' => fn ($q) => $q->latest()->limit(6)]);

        $lines = [
            "AFFAIRE {$deal->reference} : {$deal->title}",
            'Client : ' . $deal->display_name . ($deal->contact_name ? " (contact : {$deal->contact_name})" : ''),
            'Étape : ' . $deal->stage_label,
            'Besoin exprimé : ' . ($deal->need ?: 'non renseigné'),
            'Montant estimé : ' . ($deal->amount ? number_format($deal->amount, 0, ',', ' ') . ' FCFA' : 'non renseigné'),
        ];

        if ($deal->quote) {
            $lines[] = "Devis {$deal->quote->number} (statut {$deal->quote->status}) — total " . number_format($deal->quote->total_amount, 0, ',', ' ') . ' FCFA : '
                . $deal->quote->items->map(fn ($i) => "{$i->quantity} × {$i->description}")->implode(', ');
        }

        $history = $deal->activities->reverse()->map(fn ($a) => '- ' . $a->created_at->format('d/m/Y') . " ({$a->type_label}) " . Str::limit($a->body, 200))->implode("\n");
        if ($history) {
            $lines[] = "Derniers échanges :\n{$history}";
        }

        return implode("\n", $lines);
    }

    private function nextQuoteNumber(): string
    {
        $count = Quote::count() + 1;
        do {
            $number = 'DEV-' . date('Y') . '-' . str_pad($count, 4, '0', STR_PAD_LEFT);
            $count++;
        } while (Quote::where('number', $number)->exists());

        return $number;
    }

    /**
     * @return string|array Texte, ou tableau décodé si $json.
     */
    private function generate(string $prompt, bool $json = false): string|array
    {
        $key = config('services.gemini.key');
        if (empty($key)) {
            throw new RuntimeException("L'assistant IA n'est pas configuré (clé GEMINI_API_KEY manquante).");
        }

        $model = config('services.gemini.model', 'gemini-flash-latest');
        $body = [
            'systemInstruction' => ['parts' => [['text' => self::RULES]]],
            'contents' => [['role' => 'user', 'parts' => [['text' => $prompt]]]],
            'generationConfig' => ['temperature' => 0.4],
        ];
        if ($json) {
            $body['generationConfig']['responseMimeType'] = 'application/json';
        }

        // Surcharge momentanée du service (503) ou quota ponctuel (429) : on réessaie deux fois.
        $response = Http::timeout(60)
            ->retry(3, 2000, fn ($e) => $e instanceof \Illuminate\Http\Client\RequestException && in_array($e->response->status(), [429, 503], true), throw: false)
            ->withHeaders(['x-goog-api-key' => $key])
            ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent", $body);

        $text = $response->json('candidates.0.content.parts.0.text');

        if (!$response->successful() || !$text) {
            Log::error('SalesAssistant: réponse IA invalide', ['status' => $response->status(), 'body' => Str::limit($response->body(), 500)]);
            throw new RuntimeException("L'assistant IA n'a pas pu répondre. Réessayez dans un instant.");
        }

        if (!$json) {
            return trim($text);
        }

        $decoded = json_decode($text, true);
        if (!is_array($decoded)) {
            throw new RuntimeException("L'assistant IA a renvoyé une réponse inexploitable. Réessayez.");
        }

        return $decoded;
    }
}
