<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $quote->number }} - Devis</title>
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
                <h2 class="text-4xl font-black text-gray-300 uppercase mb-2">DEVIS</h2>
                <p class="text-lg font-bold text-navy-600">{{ $quote->number }}</p>
                <p class="text-sm text-gray-500">Date: {{ $quote->created_at->format('d/m/Y') }}</p>
                @if($quote->valid_until)
                <p class="text-sm text-gray-500">Validité: {{ $quote->valid_until->format('d/m/Y') }}</p>
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
                <p class="font-bold">{{ $quote->client_name }}</p>
                <p class="text-sm text-gray-600">{{ $quote->client_address }}</p>
                @if($quote->client_phone)<p class="text-sm text-gray-600">{{ $quote->client_phone }}</p>@endif
                @if($quote->client_email)<p class="text-sm text-gray-600">{{ $quote->client_email }}</p>@endif
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
                @foreach($quote->items as $item)
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
                    <span class="font-bold">{{ number_format($quote->subtotal, 0, ',', ' ') }} FCFA</span>
                </div>
                <div class="flex justify-between py-4 border-b">
                    <span class="text-lg font-bold">TOTAL</span>
                    <span class="text-xl font-black text-navy-600">{{ number_format($quote->total_amount, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>

        <!-- Footer -->
        @if($quote->notes)
        <div class="mb-10 text-sm">
            <h3 class="font-bold mb-2">Notes & Conditions</h3>
            <p class="text-gray-600 whitespace-pre-line">{{ $quote->notes }}</p>
        </div>
        @endif

        <div class="text-center text-xs text-gray-400 mt-20 border-t pt-8">
            <p>IT HOLDING SERVICES - SARL au capital de ... - NINEA ... - RCCM ...</p>
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
