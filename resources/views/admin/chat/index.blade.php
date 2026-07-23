@extends('layouts.admin')

@section('title', 'Support Chat - Admin')

@section('content')
<div class="h-[calc(100vh-180px)] flex flex-col">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-3xl font-bold text-gray-900">Support Chat Direct</h1>
        <p class="text-sm text-gray-500">Communiquez en temps réel avec vos clients et visiteurs.</p>
    </div>

    <!-- Main Chat Split View -->
    <div class="flex-grow flex bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <!-- Sidebar: Conversations List -->
        <div class="w-1/3 border-r border-gray-100 flex flex-col bg-gray-50/50">
            <!-- Search bar -->
            <div class="p-4 border-b border-gray-100 bg-white">
                <input type="text" id="search-conversations" onkeyup="filterConversations()" placeholder="Rechercher une discussion..." class="w-full text-xs border-gray-200 bg-gray-50 rounded-lg py-2.5 px-3.5 focus:ring-navy-500 focus:border-navy-500">
            </div>

            <!-- Conversations List -->
            <div class="flex-grow overflow-y-auto" id="conversations-list-container">
                @if(count($conversations) === 0)
                <div class="p-8 text-center text-gray-400 text-xs italic">
                    Aucune discussion active.
                </div>
                @endif

                @foreach($conversations as $conv)
                <button onclick="selectConversation('{{ $conv['key'] }}', '{{ addslashes($conv['user_name']) }}')" class="conversation-item w-full text-left p-4 border-b border-gray-50 flex items-center justify-between gap-3 hover:bg-white hover:shadow-sm transition-all duration-150 focus:outline-none" data-key="{{ $conv['key'] }}" data-name="{{ strtolower($conv['user_name']) }}">
                    <div class="flex items-center gap-3 overflow-hidden">
                        <div class="w-10 h-10 rounded-full bg-navy-100 border border-navy-200 flex items-center justify-center flex-shrink-0 text-navy-800 font-bold uppercase">
                            {{ substr($conv['user_name'], 0, 2) }}
                        </div>
                        <div class="overflow-hidden">
                            <h4 class="text-xs font-bold text-gray-900 truncate">{{ $conv['user_name'] }}</h4>
                            <p class="text-[10px] text-gray-400 truncate mt-0.5" id="conv-latest-{{ $conv['key'] }}">{{ $conv['latest_message'] }}</p>
                        </div>
                    </div>
                    
                    <div class="flex flex-col items-end flex-shrink-0 gap-1.5">
                        <span class="text-[9px] text-gray-400 font-medium">{{ $conv['time'] }}</span>
                        <span class="unread-badge bg-red-600 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-full {{ $conv['unread_count'] > 0 ? '' : 'hidden' }}" id="conv-unread-{{ $conv['key'] }}">
                            {{ $conv['unread_count'] }}
                        </span>
                    </div>
                </button>
                @endforeach
            </div>
        </div>

        <!-- Chat Area -->
        <div class="w-2/3 flex flex-col bg-white" id="chat-area-container">
            <!-- No Conversation Selected Placeholder -->
            <div id="no-chat-selected" class="flex-grow flex flex-col items-center justify-center text-center p-8 bg-gray-50/20">
                <div class="w-16 h-16 rounded-full bg-navy-50 flex items-center justify-center text-navy-600 mb-4 border border-navy-100">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-800 uppercase tracking-wider">Aucune Discussion Sélectionnée</h3>
                <p class="text-xs text-gray-400 max-w-sm mt-2">Veuillez sélectionner un client ou un visiteur dans la liste latérale pour démarrer ou poursuivre une discussion de support en direct.</p>
            </div>

            <!-- Active Chat Interface -->
            <div id="active-chat" class="hidden flex-grow flex flex-col overflow-hidden h-full">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between bg-gray-50/30">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-navy-900 border border-navy-950 flex items-center justify-center text-gold-500 font-bold uppercase" id="active-user-avatar">
                            --
                        </div>
                        <div>
                            <h3 class="text-sm font-bold text-gray-900" id="active-user-name">Client</h3>
                            <span class="inline-flex items-center gap-1.5 text-[9px] text-green-600 font-semibold uppercase tracking-wider">
                                <span class="w-1.5 h-1.5 bg-green-500 rounded-full"></span>
                                En discussion
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <div class="flex-grow p-6 overflow-y-auto space-y-4 bg-gray-50/20" id="admin-chat-messages">
                    <!-- Loaded dynamically -->
                </div>

                <!-- Input area -->
                <div class="p-4 border-t border-gray-100 flex items-center gap-3 bg-white">
                    <textarea id="admin-chat-input" placeholder="Saisissez votre réponse..." rows="1" class="flex-grow text-xs border-gray-200 bg-gray-50 rounded-xl py-3 px-4 focus:ring-navy-500 focus:border-navy-500 focus:outline-none resize-none" onkeyup="if(event.key === 'Enter' && !event.shiftKey) sendAdminResponse()"></textarea>
                    <button onclick="sendAdminResponse()" class="w-11 h-11 bg-navy-900 hover:bg-gold-500 hover:text-navy-900 text-gold-500 rounded-xl flex items-center justify-center shadow-lg transition-all duration-150 focus:outline-none">
                        <svg class="w-5 h-5 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let activeKey = null;
    let activeName = '';
    let adminPollInterval = null;
    let lastAdminMsgCount = 0;

    function selectConversation(key, name) {
        activeKey = key;
        activeName = name;

        // Visual states
        document.querySelectorAll('.conversation-item').forEach(item => {
            item.classList.remove('bg-white', 'shadow-sm', 'border-l-4', 'border-navy-900');
        });
        const selectedBtn = document.querySelector(`.conversation-item[data-key="${key}"]`);
        if (selectedBtn) {
            selectedBtn.classList.add('bg-white', 'shadow-sm', 'border-l-4', 'border-navy-900');
        }

        // Hide placeholder and show active chat view
        document.getElementById('no-chat-selected').classList.add('hidden');
        document.getElementById('active-chat').classList.remove('hidden');

        // Update active header details
        document.getElementById('active-user-name').innerText = name;
        document.getElementById('active-user-avatar').innerText = name.substring(0, 2).toUpperCase();

        // Clear badge counter
        const badge = document.getElementById(`conv-unread-${key}`);
        if (badge) {
            badge.classList.add('hidden');
        }

        // Fetch messages and set up polling
        fetchConversationMessages();
        
        if (adminPollInterval) {
            clearInterval(adminPollInterval);
        }
        adminPollInterval = setInterval(fetchConversationMessages, 3000);
    }

    function fetchConversationMessages() {
        if (!activeKey) return;

        fetch(`{{ url('admin/chat/messages') }}/${activeKey}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderAdminMessages(data.messages);
                }
            })
            .catch(error => console.error('Error fetching admin messages:', error));
    }

    function renderAdminMessages(messages) {
        const container = document.getElementById('admin-chat-messages');
        const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 40;

        let html = '';
        messages.forEach(msg => {
            if (msg.is_from_admin) {
                html += `
                    <div class="flex items-start gap-2.5 max-w-[85%] ml-auto justify-end">
                        <div class="bg-navy-900 text-white rounded-2xl rounded-tr-none p-3 shadow-md">
                            <p class="text-xs leading-relaxed">${escapeHtml(msg.message)}</p>
                            <span class="block text-[8px] text-gray-400 text-right mt-1 font-semibold">${msg.time}</span>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="flex items-start gap-2.5 max-w-[85%]">
                        <div class="w-8 h-8 rounded-full bg-gray-100 border border-gray-200 flex items-center justify-center flex-shrink-0 text-gray-600 font-bold text-xs uppercase">
                            ${activeName.substring(0, 2)}
                        </div>
                        <div class="bg-white border border-gray-100 rounded-2xl rounded-tl-none p-3 shadow-sm">
                            <p class="text-xs text-gray-800 leading-relaxed">${escapeHtml(msg.message)}</p>
                            <span class="block text-[8px] text-gray-400 text-right mt-1 font-semibold">${msg.time}</span>
                        </div>
                    </div>
                `;
            }
        });

        container.innerHTML = html;

        // Auto-scroll logic
        if (messages.length > lastAdminMsgCount) {
            lastAdminMsgCount = messages.length;
            scrollToAdminBottom();
        } else if (isAtBottom) {
            scrollToAdminBottom();
        }
    }

    function sendAdminResponse() {
        const input = document.getElementById('admin-chat-input');
        const text = input.value.trim();
        if (!text || !activeKey) return;

        input.value = '';

        fetch(`{{ url('admin/chat/send') }}/${activeKey}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                message: text
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update latest message text in conversation list
                const latestTxt = document.getElementById(`conv-latest-${activeKey}`);
                if (latestTxt) {
                    latestTxt.innerText = text;
                }
                fetchConversationMessages();
            }
        })
        .catch(error => console.error('Error sending admin response:', error));
    }

    function filterConversations() {
        const q = document.getElementById('search-conversations').value.toLowerCase();
        document.querySelectorAll('.conversation-item').forEach(item => {
            const name = item.getAttribute('data-name');
            if (name.includes(q)) {
                item.classList.remove('hidden');
            } else {
                item.classList.add('hidden');
            }
        });
    }

    function scrollToAdminBottom() {
        const container = document.getElementById('admin-chat-messages');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function escapeHtml(text) {
        return text
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }
</script>
@endpush
