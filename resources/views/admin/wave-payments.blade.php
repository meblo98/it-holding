@extends('layouts.admin')
@section('title', 'Paiements Wave')
@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Paiements Wave</h1>
    <p class="text-sm text-gray-500">Vérifiez chaque paiement dans votre compte Wave Business (montant et référence), puis confirmez.</p>
</div>

<div class="grid grid-cols-3 gap-4 mb-6">
    <a href="{{ route('admin.wave-payments.index', ['status' => 'submitted']) }}" class="bg-white rounded-lg shadow-sm border border-amber-100 p-4 block">
        <p class="text-xs font-bold text-amber-500 uppercase mb-1">À vérifier</p><p class="text-2xl font-black text-amber-700">{{ $stats['submitted'] }}</p></a>
    <a href="{{ route('admin.wave-payments.index', ['status' => 'awaiting']) }}" class="bg-white rounded-lg shadow-sm border border-gray-100 p-4 block">
        <p class="text-xs font-bold text-gray-400 uppercase mb-1">Pas encore payés</p><p class="text-2xl font-black text-gray-700">{{ $stats['awaiting'] }}</p></a>
    <a href="{{ route('admin.wave-payments.index', ['status' => 'confirmed']) }}" class="bg-white rounded-lg shadow-sm border border-green-100 p-4 block">
        <p class="text-xs font-bold text-green-500 uppercase mb-1">Confirmés</p><p class="text-2xl font-black text-green-700">{{ $stats['confirmed'] }}</p></a>
</div>

<div class="space-y-4">
    @forelse($payments as $p)
        <div class="bg-white rounded-lg shadow-sm border {{ $p->status === 'submitted' ? 'border-amber-300' : 'border-gray-100' }} p-5">
            <div class="flex flex-wrap items-start justify-between gap-3">
                <div>
                    <p class="font-bold text-gray-900">{{ $p->payable_label }}</p>
                    <p class="text-sm text-gray-500">{{ $p->user?->name ?? 'Client' }} · {{ $p->created_at->format('d/m/Y H:i') }}</p>
                </div>
                <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-100 text-gray-700">{{ $p->status_label }}</span>
            </div>
            <div class="mt-3 grid grid-cols-2 md:grid-cols-3 gap-3 text-sm">
                <div><span class="text-xs text-gray-400 uppercase font-bold block">Montant attendu</span><span class="font-black text-lg">{{ number_format($p->amount, 0, ',', ' ') }} FCFA</span></div>
                <div><span class="text-xs text-gray-400 uppercase font-bold block">Référence Wave</span><span class="font-mono">{{ $p->reference ?? '—' }}</span></div>
                <div>@if($p->proof_path)<a href="{{ route('admin.wave-payments.proof', $p->id) }}" target="_blank" class="font-bold text-navy-700 underline">Voir la capture</a>@endif</div>
            </div>

            @if($p->status === 'submitted')
                <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-3">
                    <form method="POST" action="{{ route('admin.wave-payments.confirm', $p->id) }}" onsubmit="return confirm('Avez-vous vu ce paiement de {{ number_format($p->amount, 0, ',', ' ') }} FCFA dans Wave ?')">
                        @csrf<button class="w-full px-4 py-2.5 bg-green-600 text-white font-bold rounded-md text-sm hover:bg-green-700">Confirmer le paiement</button>
                    </form>
                    <form method="POST" action="{{ route('admin.wave-payments.reject', $p->id) }}" class="space-y-2">
                        @csrf
                        <input name="reason" required maxlength="300" placeholder="Motif du refus (visible par le client)" class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
                        <button class="w-full px-4 py-2.5 bg-red-600 text-white font-bold rounded-md text-sm hover:bg-red-700">Refuser</button>
                    </form>
                </div>
            @endif
            @if($p->status === 'rejected' && $p->rejection_reason)<p class="mt-3 text-sm text-red-700">Motif : {{ $p->rejection_reason }}</p>@endif
        </div>
    @empty
        <div class="bg-white rounded-lg border border-gray-100 p-10 text-center text-gray-400">Aucun paiement.</div>
    @endforelse
</div>
<div class="mt-4">{{ $payments->links() }}</div>
@endsection
