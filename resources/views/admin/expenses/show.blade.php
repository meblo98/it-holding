@extends('layouts.admin')

@section('title', 'Détails de la Dépense - Admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.expenses.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-navy-600 transition-colors gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux dépenses
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Dépense #{{ $expense->id }}</h1>
        <p class="text-gray-500 text-sm mt-1">Détails complets de la dépense enregistrée le {{ $expense->expense_date->format('d/m/Y') }}.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Main Details Card -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-8 py-5 flex justify-between items-center bg-gray-50/50">
                    <span class="text-sm font-bold text-gray-500 uppercase tracking-wider">Informations Générales</span>
                    <span class="px-2.5 py-1 text-xs font-semibold rounded-full 
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
                </div>

                <div class="p-8 space-y-6">
                    <div>
                        <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Intitulé</span>
                        <h2 class="text-xl font-bold text-gray-900 mt-1">{{ $expense->title }}</h2>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-50">
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Montant</span>
                            <span class="text-2xl font-extrabold text-red-600 mt-1 block">{{ number_format($expense->amount, 2, ',', ' ') }} FCFA</span>
                        </div>
                        <div>
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block">Moyen de règlement</span>
                            <span class="text-lg font-semibold text-gray-800 mt-1 block">{{ $expense->payment_method_label }}</span>
                        </div>
                    </div>

                    @if($expense->description)
                        <div class="pt-6 border-t border-gray-50">
                            <span class="text-xs font-semibold text-gray-400 uppercase tracking-wider block mb-2">Description / Notes</span>
                            <p class="text-sm text-gray-600 leading-relaxed bg-gray-50 rounded-xl p-4 border border-gray-100/50">{{ $expense->description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Justificatif / Attachment Preview -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-8 py-5 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Pièce Justificative</h3>
                </div>
                <div class="p-8">
                    @if($expense->attachment)
                        @php
                            $extension = pathinfo($expense->attachment, PATHINFO_EXTENSION);
                            $isImage = in_array(strtolower($extension), ['jpeg', 'jpg', 'png', 'gif']);
                        @endphp

                        @if($isImage)
                            <div class="flex flex-col items-center gap-4">
                                <div class="border border-gray-100 rounded-xl overflow-hidden shadow-sm max-w-md bg-gray-50 p-2">
                                    <img src="{{ asset('storage/' . $expense->attachment) }}" alt="Pièce Justificative" class="w-full h-auto object-contain rounded-lg max-h-96">
                                </div>
                                <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                    Télécharger l'image
                                </a>
                            </div>
                        @else
                            <div class="flex items-center justify-between p-4 bg-gray-50 border border-gray-100 rounded-xl">
                                <div class="flex items-center gap-3">
                                    <div class="p-2.5 bg-red-100 text-red-600 rounded-lg">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <div class="text-sm font-semibold text-gray-900">Document PDF / Justificatif</div>
                                        <div class="text-xs text-gray-400 mt-0.5">Extension : {{ strtoupper($extension) }}</div>
                                    </div>
                                </div>
                                <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-all gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                    </svg>
                                    Ouvrir le fichier
                                </a>
                            </div>
                        @endif
                    @else
                        <div class="text-center py-6 text-sm text-gray-400 italic">Aucune pièce justificative rattachée à cette dépense.</div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar / Additional Context info -->
        <div class="space-y-6">
            <!-- Bank Account & Transaction Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Trésorerie & Banque</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($expense->bankAccount)
                        <div>
                            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Compte bancaire débité</span>
                            <div class="font-bold text-gray-900 mt-1">{{ $expense->bankAccount->name }}</div>
                            <div class="text-xs text-gray-500 mt-0.5">{{ $expense->bankAccount->bank_name }}</div>
                        </div>

                        <div class="pt-4 border-t border-gray-50">
                            <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Transaction liée</span>
                            @if($expense->bankTransaction)
                                <div class="flex items-center gap-2 mt-1.5">
                                    <span class="px-2 py-0.5 text-xs font-semibold rounded bg-red-50 text-red-600 border border-red-100">Débit</span>
                                    <span class="text-sm font-bold text-gray-800">{{ $expense->bankTransaction->reference }}</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-1">Transaction enregistrée le {{ $expense->bankTransaction->transaction_date->format('d/m/Y') }}</div>
                            @else
                                <div class="text-sm text-amber-600 font-medium mt-1">Transaction introuvable ou supprimée</div>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-4 text-sm text-gray-500 italic">
                            Dépense effectuée par Caisse (Espèces) ou autre moyen non lié à un compte bancaire.
                        </div>
                    @endif
                </div>
            </div>

            <!-- Traceability -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="border-b border-gray-100 px-6 py-4 bg-gray-50/50">
                    <h3 class="text-sm font-bold text-gray-500 uppercase tracking-wider">Traçabilité</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Enregistré par</span>
                        <div class="font-bold text-gray-900 mt-1">{{ $expense->user ? $expense->user->name : 'Système' }}</div>
                        <div class="text-xs text-gray-500 mt-0.5">{{ $expense->user ? $expense->user->email : '' }}</div>
                    </div>

                    <div class="pt-4 border-t border-gray-50">
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Créé le</span>
                        <div class="text-sm font-medium text-gray-800 mt-1">{{ $expense->created_at->format('d/m/Y à H:i') }}</div>
                    </div>

                    <div class="pt-4 border-t border-gray-50">
                        <span class="text-xs text-gray-400 font-semibold uppercase tracking-wider">Dernière mise à jour</span>
                        <div class="text-sm font-medium text-gray-800 mt-1">{{ $expense->updated_at->format('d/m/Y à H:i') }}</div>
                    </div>
                </div>
            </div>

            <!-- Danger Zone / Edit actions -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 flex flex-col gap-3">
                <a href="{{ route('admin.expenses.edit', $expense->id) }}" class="inline-flex justify-center items-center px-4 py-2.5 bg-amber-600 hover:bg-amber-700 text-white rounded-lg font-bold text-sm shadow-sm transition-all gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                    </svg>
                    Modifier la dépense
                </a>
                
                <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette dépense ? Le solde bancaire associé sera restauré.');" class="w-full">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-red-200 hover:bg-red-50 text-red-600 bg-white rounded-lg font-bold text-sm transition-all gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4.5 w-4.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Supprimer la dépense
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection
