<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\CommissionPayout;
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

            $blockedByContract = 0;

            // Contrat, retenue à la source et crédit du portefeuille pro (doc §21-22, §34-37).
            foreach ($commissions as $commission) {
                if (app(CommissionPayout::class)->payOrderCommission($commission) === 'blocked') {
                    $blockedByContract++;
                }
            }
        }

        if ($order->status === 'cancelled' && $oldStatus !== 'cancelled') {
            // Une commission bloquée faute de contrat ne doit pas être versée
            // plus tard si la commande a été annulée entre-temps.
            \App\Models\PartnerCommission::where('order_id', $order->id)
                ->whereIn('status', ['pending', 'blocked_no_contract'])
                ->update(['status' => 'cancelled']);

            $alreadyPaid = \App\Models\PartnerCommission::where('order_id', $order->id)->where('status', 'paid')->count();
        }

        $message = 'Commande mise à jour avec succès.';
        if (!empty($blockedByContract)) {
            $message .= " {$blockedByContract} commission(s) non versée(s) : le partenaire n'a pas encore accepté son contrat.";
        }
        if (!empty($alreadyPaid)) {
            $message .= " Attention : {$alreadyPaid} commission(s) avaient déjà été versées au portefeuille du partenaire pour cette commande — à régulariser manuellement.";
        }

        return redirect()->route('admin.orders.show', $order->id)->with('success', $message);
    }
}
