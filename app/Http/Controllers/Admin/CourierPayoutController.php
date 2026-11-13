<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\User;

/** Revenus des livreurs : ce qu'il reste à leur payer, et marquage « payé ». */
class CourierPayoutController extends Controller
{
    private function unpaid()
    {
        return Delivery::where('status', 'delivered')->whereNotNull('courier_fee')->whereNull('fee_paid_at');
    }

    public function index()
    {
        $rows = $this->unpaid()
            ->selectRaw('courier_id, COUNT(*) as n, SUM(courier_fee) as total')
            ->groupBy('courier_id')
            ->get();

        $couriers = User::whereIn('id', $rows->pluck('courier_id'))->get()->keyBy('id');

        $paidThisMonth = (int) Delivery::where('status', 'delivered')
            ->where('fee_paid_at', '>=', now()->startOfMonth())
            ->sum('courier_fee');

        return view('admin.courier-payouts', [
            'rows'          => $rows,
            'couriers'      => $couriers,
            'totalUnpaid'   => (int) $rows->sum('total'),
            'paidThisMonth' => $paidThisMonth,
        ]);
    }

    public function pay(User $courier)
    {
        abort_unless($courier->isLivreur(), 404);

        $amount = (int) $this->unpaid()->where('courier_id', $courier->id)->sum('courier_fee');
        $this->unpaid()->where('courier_id', $courier->id)->update(['fee_paid_at' => now()]);

        return back()->with('success', 'Paiement de ' . number_format($amount, 0, ',', ' ') . ' FCFA enregistré pour ' . $courier->name . '.');
    }
}
