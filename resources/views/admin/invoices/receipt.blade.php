<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ticket #{{ $invoice->number }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; padding: 0 !important; margin: 0 !important; }
        }
        body { font-family: 'Courier New', Courier, monospace; background-color: #f3f4f6; }
    </style>
</head>
<body class="p-4 md:p-8 flex justify-center items-start">
    <div class="w-[80mm] max-w-full bg-white p-4 shadow-md rounded border border-gray-100 text-[11px] text-black">
        
        <!-- Header -->
        <div class="text-center mb-4">
            <h1 class="text-sm font-black uppercase tracking-wider">IT HOLDING SERVICES</h1>
            <p class="text-[9px] text-gray-600">Sénégal, Dakar</p>
            <p class="text-[9px] text-gray-600">contact@itholding.sn</p>
            <p class="text-[9px] text-gray-600">+221 77 351 87 16</p>
        </div>

        <div class="border-b border-dashed border-black my-2"></div>

        <!-- Meta info -->
        <div class="space-y-1 text-[10px]">
            <div class="flex justify-between">
                <span class="font-bold">Ticket:</span>
                <span>{{ $invoice->number }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-bold">Date:</span>
                <span>{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="font-bold">Client:</span>
                <span class="truncate max-w-[120px]">{{ $invoice->client_name }}</span>
            </div>
            @if($invoice->payment_method)
            <div class="flex justify-between">
                <span class="font-bold">Mode:</span>
                <span class="uppercase">
                    @switch($invoice->payment_method)
                        @case('espece') @case('cod') Espèces @break
                        @case('cheque') Chèque @break
                        @case('bank_transfer') Virement @break
                        @case('orange_money') OM @break
                        @case('wave') Wave @break
                        @case('wallet') Portefeuille @break
                        @case('credit') Crédit Pro @break
                        @default {{ $invoice->payment_method }}
                    @endswitch
                </span>
            </div>
            @endif
        </div>

        <div class="border-b border-dashed border-black my-2"></div>

        <!-- Items Table -->
        <table class="w-full text-left text-[10px] my-2">
            <thead>
                <tr class="font-bold border-b border-gray-200">
                    <th class="pb-1">Descr</th>
                    <th class="pb-1 text-center">Qté</th>
                    <th class="pb-1 text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr class="border-b border-dotted border-gray-100">
                    <td class="py-1 max-w-[140px] truncate">{{ $item->description }}</td>
                    <td class="py-1 text-center font-mono">{{ $item->quantity }}</td>
                    <td class="py-1 text-right font-mono">{{ number_format($item->total_price, 0, ',', ' ') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-b border-dashed border-black my-2"></div>

        <!-- Totals -->
        <div class="space-y-1 text-right text-[10px] font-mono">
            <div class="flex justify-between">
                <span>Sous-total:</span>
                <span>{{ number_format($invoice->subtotal, 0, ',', ' ') }} F</span>
            </div>
            @if($invoice->tax_amount > 0)
            <div class="flex justify-between">
                <span>TVA:</span>
                <span>{{ number_format($invoice->tax_amount, 0, ',', ' ') }} F</span>
            </div>
            @endif
            <div class="flex justify-between font-bold text-xs pt-1 border-t border-dotted border-black">
                <span>{{ $invoice->tax_amount > 0 ? 'TOTAL TTC' : 'TOTAL' }}:</span>
                <span>{{ number_format($invoice->total_amount, 0, ',', ' ') }} F</span>
            </div>
        </div>

        <div class="border-b border-dashed border-black my-4"></div>

        <!-- Footer -->
        <div class="text-center text-[9px] space-y-1">
            <p class="font-bold">Merci pour votre visite !</p>
            <p>NINEA: 012012019 - RCCM: SN DKR 2025 C 11513</p>
            <p class="text-[7px] text-gray-400 mt-2">IT HOLDING SERVICES ERP</p>
        </div>

    </div>

    <!-- Print toolbar -->
    <div class="fixed bottom-4 right-4 no-print flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white text-xs px-4 py-2 rounded-full shadow hover:bg-blue-700 transition font-bold">
            Imprimer
        </button>
        <button onclick="window.close()" class="bg-gray-200 text-gray-700 text-xs px-4 py-2 rounded-full shadow hover:bg-gray-300 transition font-bold">
            Fermer
        </button>
    </div>
</body>
</html>
