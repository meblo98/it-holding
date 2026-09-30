@extends('layouts.admin')
@section('title', 'Justificatifs de retenue')
@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.tax-rules.index') }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Justificatifs de retenue</h1>
        <p class="text-sm text-gray-500 mt-0.5">Toutes les commissions et opportunités pour lesquelles une retenue à la source a été appliquée.</p>
    </div>
</div>

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    @if($items->isEmpty())
    <div class="p-10 text-center text-gray-400 text-sm">Aucune retenue appliquée pour le moment.</div>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Référence</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bénéficiaire</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Règle appliquée</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Brut</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Retenue</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Net</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($items as $item)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4 text-xs font-mono font-bold text-navy-900">{{ $item->ref }}</td>
                <td class="px-6 py-4 text-sm text-gray-700">{{ $item->partner?->name ?? '—' }}</td>
                <td class="px-6 py-4 text-xs text-gray-500">{{ $item->tax_rule?->name ?? '—' }} ({{ $item->tax_rule ? number_format($item->tax_rule->rate, 2) . '%' : '' }})</td>
                <td class="px-6 py-4 text-sm text-right text-gray-700">{{ number_format($item->gross, 0, ',', ' ') }}</td>
                <td class="px-6 py-4 text-sm text-right font-bold text-red-600">-{{ number_format($item->withholding, 0, ',', ' ') }}</td>
                <td class="px-6 py-4 text-sm text-right font-bold text-green-700">{{ number_format($item->net, 0, ',', ' ') }}</td>
                <td class="px-6 py-4 text-right">
                    <a href="{{ route('admin.tax-rules.certificate', ['type' => $item->type, 'id' => $item->id]) }}" target="_blank" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Attestation</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
