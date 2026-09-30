{{-- Navigation du module CRM. Attend : $active (pipeline, prospects, assistant) --}}
<div class="flex gap-1 border-b border-gray-200 mb-6 overflow-x-auto">
    @foreach([
        'pipeline' => ['admin.crm.index', 'Pipeline'],
        'prospects' => ['admin.crm.partner-prospects', 'Prospects du réseau'],
        'assistant' => ['admin.crm.assistant', 'Assistant commercial IA'],
    ] as $key => [$route, $label])
        <a href="{{ route($route) }}" class="px-4 py-2.5 text-sm font-semibold border-b-2 whitespace-nowrap -mb-px {{ $active === $key ? 'border-navy-600 text-navy-700' : 'border-transparent text-gray-500 hover:text-gray-700' }}">{{ $label }}</a>
    @endforeach
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif
@if(session('error'))
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-3 rounded text-sm text-red-800 font-medium">{{ session('error') }}</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif
