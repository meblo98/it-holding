@extends('layouts.admin')

@section('title', 'Gestion des Dépenses - Admin')

@section('content')
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Gestion des Dépenses</h1>
            <p class="text-gray-500 text-sm mt-1">Suivez, analysez et gérez les sorties d'argent et les frais de fonctionnement.</p>
        </div>
        <a href="{{ route('admin.expenses.create') }}" class="inline-flex items-center px-4 py-2.5 bg-navy-600 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-navy-700 active:bg-navy-900 focus:outline-none focus:border-navy-900 focus:ring ring-navy-300 transition ease-in-out duration-150 shadow-sm gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nouvelle Dépense
        </a>
    </div>

    <!-- KPIs -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <!-- Card: Total Expenses -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Total Dépenses (Filtré)</span>
                <span class="text-2xl font-bold text-red-600 mt-2 block">{{ number_format($totalAmount, 2, ',', ' ') }} FCFA</span>
            </div>
            <div class="p-3.5 bg-red-50 text-red-600 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6" />
                </svg>
            </div>
        </div>

        <!-- Card: Total Count -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Nombre de Transactions</span>
                <span class="text-2xl font-bold text-navy-900 mt-2 block">{{ $expenses->total() }}</span>
            </div>
            <div class="p-3.5 bg-navy-50 text-navy-600 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2" />
                </svg>
            </div>
        </div>

        <!-- Card: Average Expense -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex items-center justify-between">
            <div>
                <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Moyenne / Dépense</span>
                <span class="text-2xl font-bold text-gray-700 mt-2 block">
                    {{ $expenses->total() > 0 ? number_format($totalAmount / $expenses->total(), 2, ',', ' ') : '0' }} FCFA
                </span>
            </div>
            <div class="p-3.5 bg-gray-50 text-gray-500 rounded-xl">
                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
        <form action="{{ route('admin.expenses.index') }}" method="GET">
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-5 gap-4">
                <!-- Search term -->
                <div class="flex flex-col">
                    <label for="q" class="text-xs font-semibold text-gray-600 mb-1.5">Recherche</label>
                    <input type="text" name="q" id="q" value="{{ request('q') }}" placeholder="Titre, description..." class="rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500">
                </div>

                <!-- Category -->
                <div class="flex flex-col">
                    <label for="category" class="text-xs font-semibold text-gray-600 mb-1.5">Catégorie</label>
                    <select name="category" id="category" class="rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500">
                        <option value="">Toutes</option>
                        @foreach(\App\Models\Expense::CATEGORIES as $key => $value)
                            <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Payment Method -->
                <div class="flex flex-col">
                    <label for="payment_method" class="text-xs font-semibold text-gray-600 mb-1.5">Moyen de Paiement</label>
                    <select name="payment_method" id="payment_method" class="rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500">
                        <option value="">Tous</option>
                        @foreach(\App\Models\Expense::PAYMENT_METHODS as $key => $value)
                            <option value="{{ $key }}" {{ request('payment_method') === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Date Start -->
                <div class="flex flex-col">
                    <label for="start_date" class="text-xs font-semibold text-gray-600 mb-1.5">Du</label>
                    <input type="date" name="start_date" id="start_date" value="{{ request('start_date') }}" class="rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500">
                </div>

                <!-- Date End -->
                <div class="flex flex-col">
                    <label for="end_date" class="text-xs font-semibold text-gray-600 mb-1.5">Au</label>
                    <input type="date" name="end_date" id="end_date" value="{{ request('end_date') }}" class="rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500">
                </div>
            </div>

            <!-- Filter Buttons -->
            <div class="flex justify-end gap-3 mt-4 pt-4 border-t border-gray-50">
                <a href="{{ route('admin.expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-500">
                    Réinitialiser
                </a>
                <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-500">
                    Filtrer
                </button>
            </div>
        </form>
    </div>

    <!-- Expenses Table -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Date</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Titre / Motif</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Catégorie</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Règlement</th>
                        <th scope="col" class="px-6 py-3.5 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Compte Source</th>
                        <th scope="col" class="px-6 py-3.5 class text-right text-xs font-bold text-gray-500 uppercase tracking-wider pr-8">Montant</th>
                        <th scope="col" class="px-6 py-3.5 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Pièce</th>
                        <th scope="col" class="relative px-6 py-3.5">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($expenses as $expense)
                        <tr class="hover:bg-gray-50/50 transition-colors">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                {{ $expense->expense_date->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-semibold text-gray-900">{{ $expense->title }}</div>
                                @if($expense->description)
                                    <div class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ $expense->description }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    @switch($expense->category)
                                        @case('supplies') bg-blue-50 text-blue-700 @break
                                        @case('rent') bg-purple-50 text-purple-700 @break
                                        @case('salaries') bg-emerald-50 text-emerald-700 @break
                                        @case('travel') bg-amber-50 text-amber-700 @break
                                        @case('marketing') bg-pink-50 text-pink-700 @break
                                        @case('telecom') bg-indigo-50 text-indigo-700 @break
                                        @case('utilities') bg-cyan-50 text-cyan-700 @break
                                        @default bg-gray-100 text-gray-700
                                    @endswitch">
                                    {{ $expense->category_label }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600 font-medium">
                                {{ $expense->payment_method_label }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($expense->bankAccount)
                                    <div class="font-medium text-gray-800">{{ $expense->bankAccount->name }}</div>
                                    <div class="text-[10px] text-gray-400">{{ $expense->bankAccount->bank_name }}</div>
                                @else
                                    <span class="text-gray-400 italic">Non lié (Caisse)</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold text-right pr-8">
                                - {{ number_format($expense->amount, 2, ',', ' ') }} FCFA
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                @if($expense->attachment)
                                    <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="inline-flex text-gray-400 hover:text-navy-600 transition-colors" title="Voir le justificatif">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </a>
                                @else
                                    <span class="text-gray-300">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center gap-2">
                                    <a href="{{ route('admin.expenses.show', $expense->id) }}" class="text-gray-400 hover:text-navy-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Voir">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5s8.268 2.943 9.542 7c-1.274 4.057-5.065 7-9.542 7s-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                    <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="text-gray-400 hover:text-amber-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Modifier">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </a>
                                    <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ? Les comptes bancaires associés seront réajustés.');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-gray-400 hover:text-red-600 p-1.5 rounded-lg hover:bg-gray-100 transition-colors" title="Supprimer">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-10 whitespace-nowrap text-sm text-gray-500 text-center font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2zM10 8.5a.5.5 0 11-1 0 .5.5 0 011 0zm5 5a.5.5 0 11-1 0 .5.5 0 011 0z" />
                                    </svg>
                                    <span>Aucune dépense enregistrée pour le moment.</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $expenses->links() }}
    </div>
@endsection
