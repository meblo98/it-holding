@extends('layouts.app')

@section('title', 'Demande ' . $claim->number . ' - ' . config('app.name'))

@section('content')
@php
    $flow = \App\Models\CareClaim::FLOW;
    $labels = \App\Models\CareClaim::LABELS;
    $mapped = $claim->status === 'coverage_refused' ? 'coverage_approved' : $claim->status;
    $current = array_search($mapped, $flow, true);
    $terminalOff = in_array($claim->status, ['refused', 'cancelled'], true);
@endphp
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 lg:py-16">
        <div class="flex flex-col lg:flex-row gap-8">
            @include('layouts.client_sidebar')

            <main class="flex-1 space-y-6">
                <div>
                    <a href="{{ route('dashboard.care') }}" class="text-sm text-gray-500 hover:text-navy-900">← Mon Care+</a>
                    <h1 class="mt-3 text-xl font-semibold text-navy-900">Demande {{ $claim->number }}</h1>
                    <p class="text-sm text-gray-500">{{ $claim->subscription->product_name }} · déclarée le {{ $claim->created_at->format('d/m/Y') }}</p>
                </div>

                @foreach (['success', 'error'] as $flash)
                    @if (session($flash))
                        <div class="rounded-lg border px-4 py-3 text-sm {{ $flash === 'success' ? 'bg-green-50 border-green-200 text-green-700' : 'bg-red-50 border-red-200 text-red-700' }}">{{ session($flash) }}</div>
                    @endif
                @endforeach

                @if ($terminalOff)
                    <div class="rounded-lg border px-4 py-3 text-sm bg-red-50 border-red-200 text-red-700">
                        <strong>{{ $claim->status_label }}.</strong>
                        @if ($claim->refusal_reason) Motif : {{ $claim->refusal_reason }} @endif
                        <span class="block mt-1">Pour contester cette décision ou en discuter, contactez-nous en indiquant le numéro {{ $claim->number }}.</span>
                    </div>
                @endif

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Avancement</h2>
                    <ol class="space-y-3">
                        @foreach ($flow as $i => $key)
                            @php
                                $done = $current !== false && $i < $current;
                                $isCurrent = $current !== false && $i === $current && !$terminalOff;
                                $label = ($key === 'coverage_approved' && $claim->status === 'coverage_refused') ? $labels['coverage_refused'] : $labels[$key];
                            @endphp
                            <li class="flex items-center gap-3 text-sm">
                                <span class="flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold
                                    {{ $done ? 'bg-green-600 text-white' : ($isCurrent ? ($claim->status === 'coverage_refused' ? 'bg-red-600 text-white' : 'bg-gold-500 text-navy-900') : 'bg-gray-100 text-gray-400') }}">
                                    {{ $done ? '✓' : $i + 1 }}
                                </span>
                                <span class="{{ $isCurrent ? 'font-semibold text-navy-900' : ($done ? 'text-gray-600' : 'text-gray-400') }}">{{ $label }}</span>
                            </li>
                        @endforeach
                    </ol>
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6 space-y-4 text-sm">
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest">Ma déclaration</h2>
                    <div><span class="text-xs text-gray-400 block">Symptôme</span><span class="font-semibold text-navy-900">{{ $claim->symptoms }}</span></div>
                    <div><span class="text-xs text-gray-400 block">Description</span><p class="whitespace-pre-line text-gray-700">{{ $claim->description }}</p></div>
                    @if ($claim->started_at)<div><span class="text-xs text-gray-400 block">Apparu le</span>{{ $claim->started_at->format('d/m/Y') }}</div>@endif
                    <div><span class="text-xs text-gray-400 block">Remise de l'appareil</span>{{ $claim->drop_label }}</div>
                    @if (!empty($claim->photos))
                        <div class="flex flex-wrap gap-2">
                            @foreach ($claim->photos as $i => $p)
                                <a href="{{ route('dashboard.care.claim.file', [$claim->id, 'photo', $i]) }}" target="_blank" class="block w-20 h-20 rounded border border-gray-200 overflow-hidden">
                                    <img src="{{ route('dashboard.care.claim.file', [$claim->id, 'photo', $i]) }}" class="w-full h-full object-cover" alt="">
                                </a>
                            @endforeach
                        </div>
                    @endif
                    @if ($claim->video_path)
                        <a href="{{ route('dashboard.care.claim.file', [$claim->id, 'video']) }}" target="_blank" class="text-navy-900 font-semibold underline">Voir la vidéo</a>
                    @endif
                </div>

                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-6">
                    <h2 class="text-sm font-bold text-gray-400 uppercase tracking-widest mb-4">Historique</h2>
                    <ul class="space-y-3 text-sm">
                        @foreach ($claim->events->reverse() as $event)
                            <li>
                                <span class="font-semibold text-navy-900">{{ $labels[$event->status] ?? $event->status }}</span>
                                <span class="text-gray-400"> · {{ $event->created_at->format('d/m/Y H:i') }}</span>
                                @if ($event->note)<p class="text-gray-600">{{ $event->note }}</p>@endif
                            </li>
                        @endforeach
                    </ul>
                </div>

                @if ($claim->can_cancel)
                    <form method="POST" action="{{ route('dashboard.care.claim.cancel', $claim->id) }}" onsubmit="return confirm('Annuler cette demande ?')">
                        @csrf
                        <button type="submit" class="text-sm text-red-600 font-semibold underline">Annuler ma demande</button>
                    </form>
                @endif
            </main>
        </div>
    </div>
</div>
@endsection
