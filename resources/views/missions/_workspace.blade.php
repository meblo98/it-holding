{{--
    Espace projet d'une mission (doc §18-20), partagé admin / équipe.
    Attend : $mission (team, tasks, messages chargés), $isStaff (bool), $prefix (préfixe des noms de routes).
--}}
@php $me = auth()->user(); @endphp

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    {{-- Messages & fichiers --}}
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 flex flex-col">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="text-xs font-black text-navy-900 uppercase tracking-widest">Messages & fichiers</h2>
        </div>

        <div class="p-5 space-y-4 max-h-[520px] overflow-y-auto flex-1">
            @forelse($mission->messages as $msg)
                @php $mine = $msg->user_id === $me->id; @endphp
                <div class="flex {{ $mine ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[85%] rounded-xl px-4 py-3 {{ $mine ? 'bg-navy-900 text-white' : 'bg-gray-100 text-gray-800' }}">
                        <div class="text-[10px] font-bold mb-1 {{ $mine ? 'text-gold-400' : 'text-gray-500' }}">
                            {{ $msg->user?->name ?? 'Utilisateur supprimé' }}
                            @if($msg->user && $msg->user->role !== 'partner') · IT Holding @endif
                            · {{ $msg->created_at->format('d/m H:i') }}
                        </div>
                        @if($msg->body)
                            <div class="text-sm whitespace-pre-line">{{ $msg->body }}</div>
                        @endif
                        @if($msg->attachment_path)
                            <a href="{{ route($prefix . '.messages.download', $msg->id) }}" class="mt-2 inline-flex items-center gap-1 text-xs font-bold underline {{ $mine ? 'text-gold-300' : 'text-navy-700' }}">📎 {{ $msg->attachment_name }}</a>
                        @endif
                    </div>
                </div>
            @empty
                <p class="text-center text-xs text-gray-400 italic py-8">Aucun message. Utilisez cet espace pour échanger avec l'équipe et partager les livrables.</p>
            @endforelse
        </div>

        <form action="{{ route($prefix . '.messages.store', $mission->id) }}" method="POST" enctype="multipart/form-data" class="border-t border-gray-100 p-4 space-y-2">
            @csrf
            <textarea name="body" rows="2" class="w-full text-sm border border-gray-200 rounded-lg p-2.5" placeholder="Votre message...">{{ old('body') }}</textarea>
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                <input type="file" name="attachment" class="text-xs text-gray-500">
                <button type="submit" class="bg-navy-900 hover:bg-navy-800 text-white text-xs font-bold uppercase tracking-widest px-5 py-2.5 rounded-lg">Envoyer</button>
            </div>
            <p class="text-[10px] text-gray-400">Fichiers : PDF, Office, images, ZIP — 10 Mo max. Accessibles uniquement à l'équipe et à IT Holding.</p>
        </form>
    </div>

    <div class="space-y-6">
        {{-- Team --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-3">Équipe</h2>
            <ul class="space-y-2">
                @forelse($mission->teamApplications as $member)
                    <li class="flex items-center justify-between gap-2 text-sm">
                        <span class="font-bold text-gray-800">{{ $member->user->name }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold border {{ $member->status_classes }}">{{ $member->status_label }}</span>
                    </li>
                @empty
                    <li class="text-xs text-gray-400 italic">Aucun membre retenu.</li>
                @endforelse
            </ul>
        </div>

        {{-- Tasks --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <h2 class="text-xs font-black text-navy-900 uppercase tracking-widest mb-3">Tâches</h2>
            <ul class="space-y-3">
                @forelse($mission->tasks as $task)
                    @php $canEdit = $isStaff || $task->assigned_to === $me->id; @endphp
                    <li class="border border-gray-100 rounded-lg p-3">
                        <div class="text-sm font-bold {{ $task->status === 'done' ? 'text-gray-400 line-through' : 'text-gray-800' }}">{{ $task->title }}</div>
                        @if($task->description)<div class="text-xs text-gray-500 mt-0.5">{{ $task->description }}</div>@endif
                        <div class="text-[11px] text-gray-400 mt-1">
                            {{ $task->assignee?->name ?? 'Non assignée' }}
                            @if($task->due_date) · échéance {{ $task->due_date->format('d/m/Y') }}@endif
                        </div>
                        @if($canEdit)
                            <form action="{{ route($prefix . '.tasks.update', $task->id) }}" method="POST" class="mt-2">
                                @csrf @method('PUT')
                                <select name="status" onchange="this.form.submit()" class="text-xs border border-gray-200 rounded py-1 px-2">
                                    @foreach(\App\Models\MissionTask::STATUSES as $key => $label)
                                        <option value="{{ $key }}" {{ $task->status === $key ? 'selected' : '' }}>{{ $label }}</option>
                                    @endforeach
                                </select>
                            </form>
                        @else
                            <span class="inline-block mt-2 text-[10px] font-bold text-gray-500 uppercase">{{ \App\Models\MissionTask::STATUSES[$task->status] }}</span>
                        @endif
                    </li>
                @empty
                    <li class="text-xs text-gray-400 italic">Aucune tâche.</li>
                @endforelse
            </ul>

            @if($isStaff)
                <form action="{{ route($prefix . '.tasks.store', $mission->id) }}" method="POST" class="mt-4 border-t border-gray-100 pt-4 space-y-2">
                    @csrf
                    <input type="text" name="title" required class="w-full text-sm border border-gray-200 rounded-lg p-2" placeholder="Nouvelle tâche">
                    <select name="assigned_to" class="w-full text-sm border border-gray-200 rounded-lg p-2">
                        <option value="">Non assignée</option>
                        @foreach($mission->teamApplications as $member)
                            <option value="{{ $member->user_id }}">{{ $member->user->name }}</option>
                        @endforeach
                    </select>
                    <input type="date" name="due_date" class="w-full text-sm border border-gray-200 rounded-lg p-2">
                    <button type="submit" class="w-full bg-gold-600 hover:bg-gold-700 text-white text-xs font-bold uppercase tracking-widest px-4 py-2 rounded-lg">Ajouter la tâche</button>
                </form>
            @endif
        </div>
    </div>
</div>
