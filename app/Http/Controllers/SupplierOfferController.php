<?php

namespace App\Http\Controllers;

use App\Models\SupplierOffer;
use App\Services\SourcingService;
use Illuminate\Http\Request;

/** Page publique par lien secret : le fournisseur propose un prix et un délai (sans compte). */
class SupplierOfferController extends Controller
{
    private function find(string $token): SupplierOffer
    {
        return SupplierOffer::where('token', $token)
            ->with(['supplier', 'request.order.items.product'])
            ->firstOrFail();
    }

    public function show(string $token)
    {
        $offer = $this->find($token);
        $order = $offer->request->order;

        return view('pages.supplier-offer', [
            'offer'  => $offer,
            'open'   => SourcingService::isOpen($offer->request),
            'items'  => $order->items,
            'zone'   => SourcingService::cityLabel($order),
        ]);
    }

    public function submit(Request $request, string $token)
    {
        $offer = $this->find($token);

        if ($request->input('action') === 'decline') {
            $ok = SourcingService::recordDecline($offer);

            return back()->with($ok ? 'success' : 'error', $ok ? 'Merci, nous avons bien noté que vous ne pouvez pas répondre.' : 'Cette demande est clôturée.');
        }

        $data = $request->validate([
            'price'       => ['required', 'integer', 'min:1', 'max:100000000'],
            'delay_hours' => ['required', 'integer', 'min:1', 'max:720'],
            'note'        => ['nullable', 'string', 'max:300'],
        ], [
            'price.required'       => 'Indiquez votre prix total.',
            'delay_hours.required' => 'Indiquez le délai de préparation.',
        ]);

        $ok = SourcingService::recordOffer($offer, (int) $data['price'], (int) $data['delay_hours'], $data['note'] ?? null);

        return back()->with($ok ? 'success' : 'error', $ok
            ? 'Votre offre est enregistrée. Vous pouvez la modifier jusqu\'à la fin du délai.'
            : 'Cette demande est clôturée : le délai est dépassé.');
    }
}
