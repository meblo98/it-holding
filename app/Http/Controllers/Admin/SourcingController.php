<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SourcingRequest;
use App\Models\SupplierOffer;
use App\Services\SourcingService;
use Illuminate\Http\Request;

class SourcingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');

        $requests = SourcingRequest::with(['order', 'awardedOffer.supplier'])
            ->withCount([
                'offers',
                'offers as received_count' => fn ($q) => $q->whereIn('status', ['offered', 'selected', 'rejected']),
            ])
            ->when($status, fn ($q) => $q->where('status', $status))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'open'    => SourcingRequest::where('status', 'open')->count(),
            'problem' => SourcingRequest::whereIn('status', ['failed', 'needs_location'])->count(),
            'awarded' => SourcingRequest::where('status', 'awarded')->count(),
        ];

        return view('admin.sourcing-index', compact('requests', 'stats', 'status'));
    }

    public function show(SourcingRequest $sourcing)
    {
        $sourcing->load(['order.items.product', 'offers.supplier', 'awardedOffer.supplier']);

        return view('admin.sourcing-show', ['req' => $sourcing]);
    }

    public function awardNow(SourcingRequest $sourcing)
    {
        abort_unless($sourcing->status === 'open', 422);
        SourcingService::evaluate($sourcing, true);

        return back()->with('success', 'Appel d\'offres clôturé : ' . $sourcing->fresh()->status_label . '.');
    }

    public function awardManually(Request $request, SourcingRequest $sourcing)
    {
        $data = $request->validate(['offer_id' => 'required|exists:supplier_offers,id']);
        SourcingService::awardManually($sourcing, SupplierOffer::findOrFail($data['offer_id']));

        return back()->with('success', 'Offre retenue manuellement.');
    }

    public function relaunch(SourcingRequest $sourcing)
    {
        SourcingService::relaunch($sourcing);

        return back()->with('success', 'Appel d\'offres relancé.');
    }

    public function setLocation(Request $request, SourcingRequest $sourcing)
    {
        $data = $request->validate([
            'latitude'  => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        $sourcing->order->forceFill([
            'delivery_lat' => $data['latitude'], 'delivery_lng' => $data['longitude'], 'delivery_source' => 'admin',
        ])->saveQuietly();

        SourcingService::relaunch($sourcing);

        return back()->with('success', 'Position enregistrée, appel d\'offres lancé.');
    }

    public function cancel(SourcingRequest $sourcing)
    {
        abort_unless(in_array($sourcing->status, ['open', 'failed', 'needs_location'], true), 422);
        $sourcing->update(['status' => 'cancelled']);

        return back()->with('success', 'Appel d\'offres annulé.');
    }
}
