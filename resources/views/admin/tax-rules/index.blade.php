@extends('layouts.admin')
@section('title', 'Moteur fiscal')
@section('content')

<div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Moteur fiscal — Retenues à la source</h1>
        <p class="text-sm text-gray-500 mt-0.5">Aucun taux n'est figé dans le code : chaque règle ci-dessous est configurable par le comptable/fiscaliste et s'applique dès le prochain calcul.</p>
    </div>
    <div class="flex gap-2">
        <a href="{{ route('admin.tax-rules.withholdings') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-200 text-navy-700 rounded-md font-bold text-sm hover:bg-gray-50 transition shadow-sm gap-2">
            📄 Justificatifs de retenue
        </a>
        <a href="{{ route('admin.tax-rules.create') }}" class="inline-flex items-center px-4 py-2 bg-navy-600 text-white rounded-md font-bold text-sm hover:bg-navy-700 transition shadow-sm gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Nouvelle règle
        </a>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif

<div class="bg-white shadow-sm rounded-lg border border-gray-100 overflow-hidden">
    @if($rules->isEmpty())
    <div class="p-10 text-center text-gray-400 text-sm">
        Aucune règle fiscale configurée — tant qu'aucune règle n'existe, le système n'applique aucune retenue par défaut.
    </div>
    @else
    <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Règle</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Bénéficiaire</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prestation</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Taux / Seuil</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Validité</th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Actions</th>
            </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
            @foreach($rules as $rule)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="text-sm font-bold text-gray-900">{{ $rule->name }}</div>
                    <div class="text-xs text-gray-400">{{ $rule->country }}{{ $rule->notes ? ' · ' . \Illuminate\Support\Str::limit($rule->notes, 40) : '' }}</div>
                </td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ \App\Models\TaxRule::BENEFICIARY_TYPES[$rule->beneficiary_type] ?? $rule->beneficiary_type }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">{{ \App\Models\TaxRule::PRESTATION_NATURES[$rule->prestation_nature] ?? $rule->prestation_nature }}</td>
                <td class="px-6 py-4 text-sm text-gray-600">
                    @if($rule->is_exempt)
                        <span class="text-green-600 font-bold">Exonéré</span>
                    @else
                        <span class="font-bold">{{ number_format($rule->rate, 2) }}%</span>
                        @if($rule->threshold_amount)
                            <span class="text-gray-400"> · dès {{ number_format($rule->threshold_amount, 0, ',', ' ') }} FCFA</span>
                        @endif
                    @endif
                </td>
                <td class="px-6 py-4 text-xs text-gray-500">
                    {{ $rule->effective_from?->format('d/m/Y') ?? '—' }} → {{ $rule->effective_to?->format('d/m/Y') ?? '∞' }}
                </td>
                <td class="px-6 py-4">
                    @if($rule->active)
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-green-50 text-green-700 border border-green-200">Active</span>
                    @else
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-gray-100 text-gray-500 border border-gray-200">Inactive</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    <div class="flex items-center justify-end gap-1">
                        <a href="{{ route('admin.tax-rules.edit', $rule->id) }}" class="text-xs font-bold text-navy-600 bg-navy-50 hover:bg-navy-100 px-2.5 py-1 rounded transition">Modifier</a>
                        <form action="{{ route('admin.tax-rules.destroy', $rule->id) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette règle fiscale ?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="text-xs font-bold text-red-600 bg-red-50 hover:bg-red-100 px-2.5 py-1 rounded transition">Supprimer</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
