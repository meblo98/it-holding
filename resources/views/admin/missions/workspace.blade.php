@extends('layouts.admin')
@section('title', 'Projet ' . $mission->project_ref)
@section('content')

<div class="mb-6 flex items-center gap-3">
    <a href="{{ route('admin.missions.show', $mission->id) }}" class="text-gray-400 hover:text-gray-700"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg></a>
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Projet <span class="font-mono">{{ $mission->project_ref }}</span></h1>
        <p class="text-sm text-gray-500 mt-0.5">{{ $mission->title }} · {{ \App\Models\Mission::STATUSES[$mission->status] }}</p>
    </div>
</div>

@if(session('success'))
<div class="mb-4 bg-green-50 border-l-4 border-green-400 p-3 rounded text-sm text-green-800 font-medium">{{ session('success') }}</div>
@endif
@if($errors->any())
<div class="mb-4 bg-red-50 border-l-4 border-red-400 p-4 rounded">
    <ul class="list-disc list-inside text-sm text-red-700">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
</div>
@endif

@include('missions._workspace')
@endsection
