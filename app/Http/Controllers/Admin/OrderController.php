<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\TaxEngine;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $orders = Order::latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $order = Order::with(['items.product', 'client'])->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $order = Order::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
        ]);

        $oldStatus = $order->status;
        $order->update($validated);

        // Manage partner commissions if status changed
        if ($order->status === 'completed' && $oldStatus !== 'completed') {
            $commissions = \App\Models\PartnerCommission::where('order_id', $order->id)
                ->where('status', 'pending')
                ->get();

            foreach ($commissions as $commission) {
                $partner = $commission->partner;
                $beneficiaryType = $partner?->professionalProfile?->beneficiary_type ?? 'individual';

                // Moteur fiscal (doc §34-37) : la retenue à la source, si elle
                // s'applique, est calculée au moment précis où la commission
                // devient payable — jamais un taux figé dans ce contrôleur.
                $tax = app(TaxEngine::class)->calculate(
                    (float) $commission->commission_amount,
                    'commission_vente',
                    $beneficiaryType
                );

                $commission->update([
                    'status' => 'paid',
                    'tax_rule_id' => $tax['rule']?->id,
                    'withholding_amount' => $tax['withholding_amount'],
                    'net_amount' => $tax['net_amount'],
                ]);

                if ($partner) {
                    $partnerClient = \App\Models\Client::where('user_id', $partner->id)->first();
                    if (!$partnerClient) {
                        $names = explode(' ', $partner->name, 2);
                        $partnerClient = \App\Models\Client::create([
                            'user_id' => $partner->id,
                            'first_name' => $names[0] ?? 'Partner',
                            'last_name' => $names[1] ?? 'Partner',
                            'email' => $partner->email,
                            'phone' => $partner->phone ?? '770000000',
                            'wallet_balance' => 0,
                            'current_balance' => 0,
                        ]);
                    }

                    // Seul le net (après retenue éventuelle) est crédité au partenaire.
                    $partnerClient->increment('wallet_balance', $tax['net_amount']);

                    $desc = "Commission de la commande #" . $order->id;
                    if ($commission->promoCode) {
                        $desc .= " (Code: " . $commission->promoCode->code . ")";
                    } else {
                        $desc .= " (Lien direct)";
                    }
                    if ($tax['applied']) {
                        $desc .= " — retenue à la source de " . number_format($tax['withholding_amount'], 0, ',', ' ') . " FCFA appliquée";
                    }

                    \App\Models\WalletTransaction::create([
                        'client_id' => $partnerClient->id,
                        'type' => 'deposit',
                        'amount' => $tax['net_amount'],
                        'description' => $desc,
                        'transaction_date' => now(),
                        'order_id' => $order->id,
                    ]);
                }
            }
        }

        if ($order->status === 'cancelled' && $oldStatus !== 'cancelled') {
            \App\Models\PartnerCommission::where('order_id', $order->id)
                ->where('status', 'pending')
                ->update(['status' => 'cancelled']);
        }

        return redirect()->route('admin.orders.show', $order->id)->with('success', 'Commande mise à jour avec succès.');
    }
}
