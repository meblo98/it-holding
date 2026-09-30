<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $cert['ref'] }} - Attestation de retenue à la source</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; }
            .sheet { box-shadow: none !important; }
        }
        body { font-family: 'Inter', sans-serif; background-color: #f3f4f6; }
    </style>
</head>
<body class="p-4 md:p-10">
    @php
        $beneficiary = $cert['beneficiary'];
        $profile = $beneficiary->professionalProfile;
    @endphp
    <div class="sheet max-w-3xl mx-auto bg-white p-8 shadow-lg rounded-lg">
        <div class="flex justify-between items-start mb-10 border-b pb-8 gap-6">
            <div>
                <img src="{{ asset('logo.jpeg') }}" alt="Logo" class="h-16 mb-4">
                <h1 class="text-xl font-bold text-gray-900 uppercase">IT HOLDING SERVICES</h1>
                <p class="text-sm text-gray-600">Sénégal, Dakar</p>
                <p class="text-sm text-gray-600">contact@itholding.sn</p>
            </div>
            <div class="text-right">
                <h2 class="text-2xl font-black text-gray-300 uppercase mb-2 leading-tight">Attestation de<br>retenue à la source</h2>
                <p class="text-lg font-bold text-gray-800 font-mono">{{ $cert['ref'] }}</p>
                <p class="text-sm text-gray-500">Opération du {{ $cert['date']?->format('d/m/Y') }}</p>
                <p class="text-sm text-gray-500">Éditée le {{ now()->format('d/m/Y') }}</p>
            </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-10 mb-10">
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Bénéficiaire</p>
                <p class="text-sm font-bold text-gray-900">{{ $profile?->company_name ?: $beneficiary->name }}</p>
                @if($profile?->company_name)<p class="text-sm text-gray-600">Représenté par {{ $profile->legal_representative ?: $beneficiary->name }}</p>@endif
                <p class="text-sm text-gray-600">{{ $beneficiary->email }}</p>
                @if($profile?->pro_id)<p class="text-sm text-gray-600">ID Pro : {{ $profile->pro_id }}</p>@endif
                <p class="text-sm text-gray-600">NINEA : {{ $profile?->ninea ?: 'non communiqué' }}</p>
                @if($profile?->rccm)<p class="text-sm text-gray-600">RCCM : {{ $profile->rccm }}</p>@endif
                <p class="text-sm text-gray-600">Statut : {{ \App\Models\ProfessionalProfile::BENEFICIARY_TYPES[$profile?->beneficiary_type ?? 'individual'] }}</p>
            </div>
            <div>
                <p class="text-xs font-bold text-gray-400 uppercase mb-2">Nature de l'opération</p>
                <p class="text-sm text-gray-900">{{ $cert['nature'] }}</p>
                @if($cert['rule'])
                    <p class="text-xs text-gray-500 mt-2">Règle appliquée : {{ $cert['rule']->name }}</p>
                    @if($cert['rule']->notes)<p class="text-xs text-gray-400 italic mt-1">{{ $cert['rule']->notes }}</p>@endif
                @endif
            </div>
        </div>

        <table class="w-full text-sm mb-10">
            <tbody>
                <tr class="border-b border-gray-100">
                    <td class="py-3 text-gray-500">Montant brut (HT)</td>
                    <td class="py-3 text-right font-bold text-gray-900">{{ number_format($cert['gross'], 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="py-3 text-gray-500">Taux de retenue appliqué</td>
                    <td class="py-3 text-right font-bold text-gray-900">{{ $cert['rule'] ? number_format($cert['rule']->rate, 2, ',', ' ') . ' %' : '—' }}</td>
                </tr>
                <tr class="border-b border-gray-100">
                    <td class="py-3 text-gray-500">Retenue à la source</td>
                    <td class="py-3 text-right font-bold text-red-600">− {{ number_format($cert['withholding'], 0, ',', ' ') }} FCFA</td>
                </tr>
                <tr>
                    <td class="py-3 text-gray-900 font-bold">Net versé au bénéficiaire</td>
                    <td class="py-3 text-right font-black text-lg text-gray-900">{{ number_format($cert['net'], 0, ',', ' ') }} FCFA</td>
                </tr>
            </tbody>
        </table>

        <p class="text-xs text-gray-400 leading-relaxed border-t pt-4">
            IT Holding Services atteste avoir prélevé la retenue à la source ci-dessus sur le paiement effectué au bénéficiaire mentionné, selon la règle fiscale en vigueur à la date de l'opération. Ce document ne dispense pas le bénéficiaire de ses propres obligations déclaratives.
        </p>

        <button onclick="window.print()" class="no-print mt-8 bg-gray-900 text-white font-bold px-6 py-2.5 rounded-md hover:bg-gray-700 transition text-sm">Imprimer / Enregistrer en PDF</button>
    </div>
</body>
</html>
