<?php

namespace App\Http\Controllers;

use App\Models\PartnerProspect;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PartnerOpportunityController extends Controller
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
     * Display the apporteur d'affaires opportunities: "dossiers d'opportunité"
     * distinct from the day-to-day CRM prospects (doc §4-5, §30-33).
     */
    public function index()
    {
        $this->checkPartner();

        $user = Auth::user();
        $opportunities = $user->prospects()
            ->opportunities()
            ->orderBy('created_at', 'desc')
            ->get();

        return view('pages.shop.partner.opportunities', compact('user', 'opportunities'));
    }

    /**
     * Declare a new business opportunity.
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
            'notes' => 'nullable|string|max:2000',
        ]);

        if (empty($validated['phone']) && empty($validated['email']) && empty($validated['company'])) {
            return back()->withInput()->with('error', "Merci d'indiquer au moins un téléphone, un email ou une société pour identifier l'opportunité.");
        }

        // Règle d'attribution anti-doublon (doc §32) : on cherche si une autre
        // opportunité active correspond déjà à ce téléphone, cet email ou cette société.
        $duplicate = PartnerProspect::findDuplicateOpportunity(
            $validated['phone'] ?? null,
            $validated['email'] ?? null,
            $validated['company'] ?? null
        );

        $opportunity = PartnerProspect::create(array_merge($validated, [
            'partner_id' => Auth::id(),
            'entry_type' => 'opportunity',
            'status' => 'new',
            'opportunity_ref' => PartnerProspect::generateOpportunityRef(),
            'duplicate_of_id' => $duplicate?->id,
            'duplicate_status' => $duplicate ? 'flagged' : null,
        ]));

        if ($duplicate) {
            return redirect()->route('dashboard.partner.opportunities')->with('warning',
                "Opportunité déjà enregistrée : ce prospect a déjà été déclaré (réf. {$duplicate->opportunity_ref}) le "
                . $duplicate->created_at->format('d/m/Y') . ". Votre dossier {$opportunity->opportunity_ref} a bien été "
                . "enregistré mais est soumis à l'arbitrage d'IT Holding."
            );
        }

        return redirect()->route('dashboard.partner.opportunities')
            ->with('success', "Opportunité {$opportunity->opportunity_ref} enregistrée avec succès.");
    }
}
