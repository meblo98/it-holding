<?php

namespace App\Http\Controllers;

use App\Models\PurchaseOrder;

/** Bon de commande du fournisseur : page par lien secret, avec QR code et code de remise. */
class PurchaseOrderController extends Controller
{
    public function show(string $token)
    {
        $bon = PurchaseOrder::where('supplier_token', $token)
            ->with(['supplier', 'order.items.product', 'order.delivery.courier'])
            ->firstOrFail();

        return view('pages.purchase-order', [
            'bon'      => $bon,
            'items'    => $bon->order->items,
            'delivery' => $bon->order->delivery,
        ]);
    }
}
