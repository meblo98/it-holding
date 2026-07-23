<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
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
                $commission->update(['status' => 'paid']);

                $partner = $commission->partner;
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

                    $partnerClient->increment('wallet_balance', $commission->commission_amount);

                    \App\Models\WalletTransaction::create([
                        'client_id' => $partnerClient->id,
                        'type' => 'deposit',
                        'amount' => $commission->commission_amount,
                        'description' => "Commission de la commande #" . $order->id . " (Code: " . $commission->promoCode->code . ")",
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
