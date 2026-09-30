<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contract;
use App\Models\User;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    public function index()
    {
        $contracts = Contract::withCount('acceptances')->orderBy('type')->orderByDesc('version')->get();

        return view('admin.network-contracts.index', compact('contracts'));
    }

    public function create()
    {
        return view('admin.network-contracts.form', ['contract' => new Contract(), 'partnerTypes' => $this->typeOptions()]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        $validated['created_by'] = auth()->id();

        $contract = Contract::create($validated);

        if ($contract->status === 'active') {
            $this->archiveOtherActive($contract);
        }

        return redirect()->route('admin.network-contracts.index')->with('success', 'Contrat créé.');
    }

    public function show(Contract $contract)
    {
        $contract->load(['acceptances.user']);

        return view('admin.network-contracts.show', compact('contract'));
    }

    public function edit(Contract $contract)
    {
        abort_if($contract->isLocked(), 403, "Ce contrat a déjà des acceptations ou n'est plus un brouillon : créez une nouvelle version plutôt que de le modifier.");

        return view('admin.network-contracts.form', ['contract' => $contract, 'partnerTypes' => $this->typeOptions()]);
    }

    public function update(Request $request, Contract $contract)
    {
        abort_if($contract->isLocked(), 403, "Ce contrat a déjà des acceptations ou n'est plus un brouillon : créez une nouvelle version plutôt que de le modifier.");

        $contract->update($this->validated($request));

        if ($contract->status === 'active') {
            $this->archiveOtherActive($contract);
        }

        return redirect()->route('admin.network-contracts.index')->with('success', 'Contrat mis à jour.');
    }

    public function destroy(Contract $contract)
    {
        abort_if($contract->acceptances()->exists(), 403, 'Impossible de supprimer un contrat déjà accepté par au moins un partenaire.');

        $contract->delete();

        return redirect()->route('admin.network-contracts.index')->with('success', 'Contrat supprimé.');
    }

    private function typeOptions(): array
    {
        return ['all' => 'Toutes catégories'] + User::PARTNER_TYPES;
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'type' => 'required|in:' . implode(',', array_keys($this->typeOptions())),
            'version' => 'required|string|max:20',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'status' => 'required|in:' . implode(',', array_keys(Contract::STATUSES)),
            'effective_from' => 'nullable|date',
        ]);
    }

    /**
     * Only one active contract per type at a time — activating a new one
     * archives whichever was previously active for that same type.
     */
    private function archiveOtherActive(Contract $contract): void
    {
        Contract::where('type', $contract->type)
            ->where('id', '!=', $contract->id)
            ->where('status', 'active')
            ->update(['status' => 'archived']);
    }
}
