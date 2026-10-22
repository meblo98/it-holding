<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CareClaim;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class CareClaimAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status');
        $search = $request->input('search');

        $claims = CareClaim::with('subscription')
            ->when($status === 'open', fn ($q) => $q->open())
            ->when($status && $status !== 'open', fn ($q) => $q->where('status', $status))
            ->when($search, fn ($q, $s) => $q->where(fn ($q) => $q
                ->where('number', 'like', "%$s%")
                ->orWhereHas('subscription', fn ($q) => $q
                    ->where('client_name', 'like', "%$s%")
                    ->orWhere('number', 'like', "%$s%")
                    ->orWhere('product_name', 'like', "%$s%"))))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'open'     => CareClaim::open()->count(),
            'received' => CareClaim::where('status', 'received')->count(),
            'total'    => CareClaim::count(),
        ];

        return view('admin.care.claims-index', compact('claims', 'stats', 'status', 'search'));
    }

    public function show(CareClaim $claim)
    {
        $claim->load(['subscription', 'events.user']);

        return view('admin.care.claims-show', compact('claim'));
    }

    public function updateStatus(Request $request, CareClaim $claim)
    {
        $allowed = CareClaim::TRANSITIONS[$claim->status] ?? [];

        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', $allowed ?: ['__none__'])],
            'note'   => ['nullable', 'string', 'max:500'],
        ]);

        $claim->update(['status' => $data['status']]);
        $claim->logEvent($data['status'], $data['note'] ?? null, Auth::id());

        return back()->with('success', 'Statut mis à jour : ' . $claim->status_label . '.');
    }

    public function refuse(Request $request, CareClaim $claim)
    {
        abort_unless($claim->is_open && in_array($claim->status, ['received', 'checking', 'awaiting_device', 'diagnosing'], true), 422);

        $data = $request->validate([
            'reason' => ['required', 'string', 'max:500'],
        ], ['reason.required' => 'Indiquez le motif du refus : il sera communiqué au client.']);

        $claim->update(['status' => 'refused', 'refusal_reason' => $data['reason']]);
        $claim->logEvent('refused', $data['reason'], Auth::id());

        return back()->with('success', 'Demande refusée. Le client verra le motif.');
    }

    public function file(CareClaim $claim, string $type, int $index = 0)
    {
        $path = $type === 'video' ? $claim->video_path : ($claim->photos[$index] ?? null);
        abort_unless($path && Storage::disk('local')->exists($path), 404);

        return response()->file(Storage::disk('local')->path($path));
    }
}
