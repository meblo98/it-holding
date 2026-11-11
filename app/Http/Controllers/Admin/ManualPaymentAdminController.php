<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ManualPayment;
use App\Services\ManualPaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ManualPaymentAdminController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->input('status', 'submitted');

        $payments = ManualPayment::with(['payable', 'user'])
            ->when($status !== 'all', fn ($q) => $q->where('status', $status))
            ->orderByRaw("status = 'submitted' desc")
            ->latest('submitted_at')
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'submitted' => ManualPayment::where('status', 'submitted')->count(),
            'confirmed' => ManualPayment::where('status', 'confirmed')->count(),
            'awaiting'  => ManualPayment::where('status', 'awaiting')->count(),
        ];

        return view('admin.wave-payments', compact('payments', 'stats', 'status'));
    }

    public function confirm(ManualPayment $payment)
    {
        ManualPaymentService::confirm($payment, Auth::id());

        return back()->with('success', 'Paiement confirmé : ' . $payment->payable_label . ' est activé.');
    }

    public function reject(Request $request, ManualPayment $payment)
    {
        $data = $request->validate(['reason' => 'required|string|max:300'], [
            'reason.required' => 'Indiquez le motif du refus : il sera affiché au client.',
        ]);

        ManualPaymentService::reject($payment, Auth::id(), $data['reason']);

        return back()->with('success', 'Paiement refusé. Le client peut corriger sa référence.');
    }

    public function proof(ManualPayment $payment)
    {
        abort_unless($payment->proof_path && Storage::disk('local')->exists($payment->proof_path), 404);

        return response()->file(Storage::disk('local')->path($payment->proof_path));
    }
}
