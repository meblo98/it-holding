@extends('layouts.admin')

@section('title', 'Modifier la Dépense - Admin')

@section('content')
    <div class="mb-8">
        <a href="{{ route('admin.expenses.index') }}" class="inline-flex items-center text-sm font-semibold text-gray-500 hover:text-navy-600 transition-colors gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Retour aux dépenses
        </a>
        <h1 class="text-3xl font-bold text-gray-900 mt-2">Modifier la Dépense #{{ $expense->id }}</h1>
        <p class="text-gray-500 text-sm mt-1">Modifiez les informations. Le solde du compte bancaire associé sera automatiquement réajusté.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden max-w-4xl" x-data="{ paymentMethod: '{{ old('payment_method', $expense->payment_method) }}' }">
        <form action="{{ route('admin.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data" class="p-8">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Title / Motif -->
                <div class="col-span-1 md:col-span-2">
                    <label for="title" class="block text-sm font-semibold text-gray-700 mb-1.5">Titre / Motif de la dépense <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" value="{{ old('title', $expense->title) }}" required placeholder="Ex: Achat cartouches d'encre HP, Loyer Juillet 2026..." class="w-full rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500 @error('title') border-red-300 ring-1 ring-red-300 @enderror">
                    @error('title')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Amount -->
                <div>
                    <label for="amount" class="block text-sm font-semibold text-gray-700 mb-1.5">Montant (FCFA) <span class="text-red-500">*</span></label>
                    <div class="relative rounded-lg shadow-sm">
                        <input type="number" name="amount" id="amount" value="{{ old('amount', $expense->amount) }}" step="0.01" min="0.01" required placeholder="0.00" class="w-full rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500 @error('amount') border-red-300 ring-1 ring-red-300 @enderror">
                    </div>
                    @error('amount')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Date -->
                <div>
                    <label for="expense_date" class="block text-sm font-semibold text-gray-700 mb-1.5">Date de la dépense <span class="text-red-500">*</span></label>
                    <input type="date" name="expense_date" id="expense_date" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d')) }}" required class="w-full rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500 @error('expense_date') border-red-300 ring-1 ring-red-300 @enderror">
                    @error('expense_date')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-1.5">Catégorie <span class="text-red-500">*</span></label>
                    <select name="category" id="category" required class="w-full rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500 @error('category') border-red-300 ring-1 ring-red-300 @enderror">
                        <option value="">Sélectionner une catégorie</option>
                        @foreach(\App\Models\Expense::CATEGORIES as $key => $value)
                            <option value="{{ $key }}" {{ old('category', $expense->category) === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('category')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Payment Method -->
                <div>
                    <label for="payment_method" class="block text-sm font-semibold text-gray-700 mb-1.5">Moyen de règlement <span class="text-red-500">*</span></label>
                    <select name="payment_method" id="payment_method" x-model="paymentMethod" required class="w-full rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500 @error('payment_method') border-red-300 ring-1 ring-red-300 @enderror">
                        @foreach(\App\Models\Expense::PAYMENT_METHODS as $key => $value)
                            <option value="{{ $key }}" {{ old('payment_method', $expense->payment_method) === $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                    @error('payment_method')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Bank Account Selection -->
                <div class="col-span-1 md:col-span-2" x-show="paymentMethod === 'bank_transfer' || paymentMethod === 'check' || paymentMethod === 'card'" x-transition x-cloak>
                    <label for="bank_account_id" class="block text-sm font-semibold text-gray-700 mb-1.5">Compte bancaire émetteur <span class="text-red-500">*</span></label>
                    <select name="bank_account_id" id="bank_account_id" :required="paymentMethod === 'bank_transfer' || paymentMethod === 'check' || paymentMethod === 'card'" class="w-full rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500 @error('bank_account_id') border-red-300 ring-1 ring-red-300 @enderror">
                        <option value="">Sélectionner le compte à débiter</option>
                        @foreach($bankAccounts as $account)
                            <option value="{{ $account->id }}" {{ old('bank_account_id', $expense->bank_account_id) == $account->id ? 'selected' : '' }}>
                                {{ $account->name }} (Solde actuel : {{ number_format($account->current_balance, 2, ',', ' ') }} FCFA)
                            </option>
                        @endforeach
                    </select>
                    @error('bank_account_id')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Attachment Piece -->
                <div class="col-span-1 md:col-span-2">
                    <label for="attachment" class="block text-sm font-semibold text-gray-700 mb-1.5">Pièce justificative (Remplacera l'ancienne pièce si fournie. Max 5Mo)</label>
                    <input type="file" name="attachment" id="attachment" class="w-full rounded-lg border border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500 @error('attachment') border-red-300 ring-1 ring-red-300 @enderror">
                    @if($expense->attachment)
                        <div class="mt-2.5 flex items-center gap-2 text-xs text-gray-500">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.414a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                            <span>Pièce actuelle : </span>
                            <a href="{{ asset('storage/' . $expense->attachment) }}" target="_blank" class="text-navy-600 hover:underline font-semibold">Afficher le fichier</a>
                        </div>
                    @endif
                    @error('attachment')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description / Notes -->
                <div class="col-span-1 md:col-span-2">
                    <label for="description" class="block text-sm font-semibold text-gray-700 mb-1.5">Notes / Description complémentaire</label>
                    <textarea name="description" id="description" rows="4" placeholder="Détails supplémentaires concernant la dépense..." class="w-full rounded-lg border-gray-200 text-sm focus:ring-navy-500 focus:border-navy-500 @error('description') border-red-300 ring-1 ring-red-300 @enderror">{{ old('description', $expense->description) }}</textarea>
                    @error('description')
                        <p class="text-red-500 text-xs mt-1.5 font-medium">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Submit buttons -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.expenses.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-500">
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center px-5 py-2.5 border border-transparent rounded-lg text-sm font-medium text-white bg-navy-600 hover:bg-navy-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-navy-500 shadow-sm">
                    Mettre à jour la dépense
                </button>
            </div>
        </form>
    </div>
@endsection
