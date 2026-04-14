<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $invoice->number }} - Facture</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .print-shadow-none { shadow: none !important; }
        }
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="p-4 md:p-10">
    <div class="max-w-4xl mx-auto bg-white p-8 shadow-lg rounded-lg print-shadow-none">
        <!-- Header -->
        <div class="flex justify-between items-start mb-10 border-b pb-8">
            <div>
                <img src="{{ asset('logo.jpeg') }}" alt="Logo" class="h-16 mb-4">
                <h1 class="text-2xl font-bold text-gray-900 uppercase">IT HOLDING SERVICES</h1>
                <p class="text-sm text-gray-600">Sénégal, Dakar</p>
                <p class="text-sm text-gray-600">contact@itholding.sn</p>
                <p class="text-sm text-gray-600">+221 XX XXX XX XX</p>
            </div>
            <div class="text-right">
                <h2 class="text-4xl font-black text-gray-300 uppercase mb-2">FACTURE</h2>
                <p class="text-lg font-bold text-navy-600">{{ $invoice->number }}</p>
                <p class="text-sm text-gray-500">Date: {{ $invoice->created_at->format('d/m/Y') }}</p>
                @if($invoice->due_date)
                <p class="text-sm text-gray-500">Échéance: {{ $invoice->due_date->format('d/m/Y') }}</p>
                @endif
            </div>
        </div>

        <!-- Addresses -->
        <div class="grid grid-cols-2 gap-10 mb-10">
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Émetteur</h3>
                <p class="font-bold">IT HOLDING SERVICES</p>
                <p class="text-sm text-gray-600">Dakar, Sénégal</p>
            </div>
            <div>
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-3">Client</h3>
                <p class="font-bold">{{ $invoice->client_name }}</p>
                <p class="text-sm text-gray-600">{{ $invoice->client_address }}</p>
                @if($invoice->client_phone)<p class="text-sm text-gray-600">{{ $invoice->client_phone }}</p>@endif
                @if($invoice->client_email)<p class="text-sm text-gray-600">{{ $invoice->client_email }}</p>@endif
            </div>
        </div>

        <!-- Table -->
        <table class="w-full mb-10 border-collapse">
            <thead>
                <tr class="bg-navy-600 text-white">
                    <th class="py-3 px-4 text-left font-bold uppercase text-xs">Description</th>
                    <th class="py-3 px-4 text-center font-bold uppercase text-xs">Qté</th>
                    <th class="py-3 px-4 text-right font-bold uppercase text-xs">Prix Unitaire</th>
                    <th class="py-3 px-4 text-right font-bold uppercase text-xs">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($invoice->items as $item)
                <tr>
                    <td class="py-4 px-4 text-sm">{{ $item->description }}</td>
                    <td class="py-4 px-4 text-sm text-center">{{ $item->quantity }}</td>
                    <td class="py-4 px-4 text-sm text-right">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                    <td class="py-4 px-4 text-sm text-right font-bold">{{ number_format($item->total_price, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="flex justify-end mb-10">
            <div class="w-64">
                <div class="flex justify-between py-2 border-b">
                    <span class="text-gray-600">Sous-total</span>
                    <span class="font-bold">{{ number_format($invoice->subtotal, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between py-4 border-b">
                    <span class="text-lg font-bold">TOTAL</span>
                    <span class="text-xl font-black text-navy-600">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Payment Info -->
        <div class="mt-10 p-6 bg-gray-50 border rounded-lg text-sm">
            <h3 class="font-bold mb-2 uppercase text-xs text-gray-400 tracking-widest">Informations de paiement</h3>
            <p class="mb-1 text-gray-700 font-medium">Bénéficiaire: <span class="text-navy-600">IT HOLDING SERVICES</span></p>
            <p class="text-gray-700 font-medium">RIB: <span class="bg-gray-200 px-2 py-0.5 rounded font-mono">SNXXX XXXX XXXXXXX XXXX XX</span></p>
        </div>

        <!-- Footer -->
        @if($invoice->notes)
        <div class="mt-10 text-sm">
            <h3 class="font-bold mb-2">Notes</h3>
            <p class="text-gray-600 whitespace-pre-line">{{ $invoice->notes }}</p>
        </div>
        @endif

        <div class="text-center text-xs text-gray-400 mt-20 border-t pt-8">
            <p>IT HOLDING SERVICES - NINEA 012012019 - RCCM SN DKR 2025 C 11513</p>
        </div>
    </div>

    <!-- Toolbar -->
    <div class="fixed bottom-8 right-8 no-print flex gap-2">
        <button onclick="window.print()" class="bg-navy-600 text-white px-6 py-3 rounded-full shadow-2xl font-bold hover:bg-navy-700 transition">
            Imprimer / PDF
        </button>
        <button onclick="window.close()" class="bg-gray-200 text-gray-700 px-6 py-3 rounded-full shadow-2xl font-bold hover:bg-gray-300 transition">
            Fermer
        </button>
    </div>
</body>
</html>
