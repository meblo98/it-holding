<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ProWalletEntry;
use App\Models\WithdrawalRequest;
use App\Services\ProWallet;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class WithdrawalController extends Controller
{
    /**
     * Demandes de retrait des professionnels (doc §37 : Portefeuille → Paiement).
     */
    public function index(Request $request)
    {
        $status = $request->input('status', 'requested');

        $withdrawals = WithdrawalRequest::with(['user.professionalProfile', 'processor'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $counts = WithdrawalRequest::selectRaw('status, COUNT(*) as n, SUM(amount) as total')->groupBy('status')->get()->keyBy('status');

        return view('admin.withdrawals.index', compact('withdrawals', 'counts', 'status'));
    }

    public function approve(WithdrawalRequest $withdrawal)
    {
        abort_unless($withdrawal->status === 'requested', 422, 'Seule une demande en attente peut être approuvée.');

        $withdrawal->update(['status' => 'approved', 'processed_by' => auth()->id(), 'processed_at' => now()]);

        return back()->with('success', "Retrait {$withdrawal->reference} approuvé : à verser.");
    }

    public function markPaid(Request $request, WithdrawalRequest $withdrawal)
    {
        abort_unless(in_array($withdrawal->status, ['requested', 'approved'], true), 422, 'Ce retrait a déjà été traité.');

        $validated = $request->validate([
            'payment_reference' => 'required|string|max:100',
            'admin_note' => 'nullable|string|max:1000',
        ], [
            'payment_reference.required' => 'Indiquez la référence de la transaction (Wave, Orange Money, virement…).',
        ]);

        $withdrawal->update($validated + ['status' => 'paid', 'processed_by' => auth()->id(), 'processed_at' => now()]);

        return back()->with('success', "Retrait {$withdrawal->reference} marqué comme versé.");
    }

    public function reject(Request $request, WithdrawalRequest $withdrawal, ProWallet $wallet)
    {
        abort_unless(in_array($withdrawal->status, ['requested', 'approved'], true), 422, 'Ce retrait a déjà été traité.');

        $validated = $request->validate(['admin_note' => 'required|string|max:1000'], [
            'admin_note.required' => 'Indiquez le motif du refus : il sera visible par le professionnel.',
        ]);

        $wallet->rejectWithdrawal($withdrawal, auth()->user(), $validated['admin_note']);

        return back()->with('success', "Retrait {$withdrawal->reference} refusé, montant recrédité au portefeuille.");
    }

    /**
     * Export comptable du registre des portefeuilles pro sur une période.
     */
    public function export(Request $request): StreamedResponse
    {
        $validated = $request->validate([
            'from' => 'required|date',
            'to' => 'required|date|after_or_equal:from',
        ]);

        $filename = "portefeuilles-pro_{$validated['from']}_{$validated['to']}.csv";

        return response()->streamDownload(function () use ($validated) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // BOM : accents corrects à l'ouverture dans Excel
            fputcsv($out, ['Date', 'Professionnel', 'ID Pro', 'NINEA', 'Sens', 'Nature', 'Libellé', 'Brut', 'Retenue', 'Net'], ';');

            ProWalletEntry::with(['user.professionalProfile'])
                ->whereBetween('created_at', [$validated['from'] . ' 00:00:00', $validated['to'] . ' 23:59:59'])
                ->orderBy('created_at')
                ->chunk(500, function ($entries) use ($out) {
                    foreach ($entries as $e) {
                        fputcsv($out, [
                            $e->created_at->format('d/m/Y H:i'),
                            $e->user?->name,
                            $e->user?->professionalProfile?->pro_id,
                            $e->user?->professionalProfile?->ninea,
                            $e->isCredit() ? 'Crédit' : 'Débit',
                            $e->source_label,
                            $e->description,
                            number_format((float) $e->gross_amount, 0, ',', ''),
                            number_format((float) $e->withholding_amount, 0, ',', ''),
                            number_format($e->signed_amount, 0, ',', ''),
                        ], ';');
                    }
                });

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }
}
