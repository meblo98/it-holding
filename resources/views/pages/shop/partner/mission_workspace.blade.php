@extends('layouts.app')

@section('title', 'Projet ' . $mission->project_ref . ' - ' . config('app.name'))

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="bg-white border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs text-gray-400 gap-2 items-center italic">
                <a href="{{ route('dashboard.partner.missions') }}" class="hover:text-navy-900 uppercase tracking-wider">Missions</a>
                <span>›</span>
                <a href="{{ route('dashboard.partner.missions.show', $mission->id) }}" class="hover:text-navy-900 font-mono">{{ $mission->reference }}</a>
                <span>›</span>
                <span class="text-navy-900 font-bold uppercase tracking-wider">Espace projet</span>
            </nav>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 space-y-6">
        <div>
            <span class="text-gold-600 text-[10px] font-black uppercase tracking-widest">Projet {{ $mission->project_ref }}</span>
            <h1 class="text-2xl font-black text-navy-900 uppercase italic">{{ $mission->title }}</h1>
        </div>

        @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg text-xs font-bold italic">{{ session('success') }}</div>
        @endif
        @if($errors->any())
        <ul class="bg-red-50 border border-red-200 rounded-lg p-3 text-xs text-red-700 list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        @endif

        @include('missions._workspace')
    </div>
</div>
@endsection
