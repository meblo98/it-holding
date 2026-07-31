<!-- Support Chat Widget -->
<div id="support-chat-widget" class="fixed bottom-6 right-6 z-50 font-sans">
    <!-- Chat Button Bubble -->
    <button id="chat-bubble" onclick="toggleChatWindow()" class="relative flex items-center justify-center w-14 h-14 bg-navy-900 hover:bg-gold-500 hover:text-navy-900 text-gold-500 rounded-full shadow-2xl transition-all duration-300 transform hover:scale-110 focus:outline-none border border-gold-500/20">
        <!-- SVG Chat Icon -->
        <svg id="chat-icon-open" class="w-6 h-6 transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
        </svg>
        <!-- Close Icon (hidden initially) -->
        <svg id="chat-icon-close" class="w-6 h-6 hidden transition-all duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
        </svg>
        <!-- Unread Badge Dot -->
        <span id="chat-unread-badge" class="absolute -top-1 -right-1 w-4 h-4 bg-red-600 rounded-full border-2 border-navy-900 hidden animate-pulse"></span>
    </button>

    <!-- Chat Window Container -->
    <div id="chat-window" class="hidden absolute bottom-18 right-0 w-80 sm:w-96 h-[480px] bg-white/90 dark:bg-navy-900/95 backdrop-blur-md rounded-2xl shadow-2xl border border-gray-100 dark:border-gray-800 flex flex-col overflow-hidden transition-all duration-300 transform translate-y-4 scale-95 opacity-0 origin-bottom-right">
        <!-- Header -->
        <div class="bg-navy-900 text-white px-6 py-4 flex items-center justify-between border-b border-gray-100/10">
            <div class="flex items-center gap-3">
                <div class="relative w-8 h-8 rounded-full bg-gold-500/20 border border-gold-500/30 flex items-center justify-center">
                    <span class="text-gold-500 font-bold text-xs">IT</span>
                    <span class="absolute bottom-0 right-0 w-2 h-2 bg-green-500 rounded-full border border-navy-900"></span>
                </div>
                <div>
                    <h3 class="text-xs font-black tracking-widest uppercase text-gold-500">Support Client</h3>
                    <p class="text-[9px] text-gray-400 font-medium">Répond généralement en quelques minutes</p>
                </div>
            </div>
            <button onclick="toggleChatWindow()" class="text-gray-400 hover:text-white transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </div>

        <!-- Name Input Form for guest users before starting chat -->
        @if(!Auth::check())
        <div id="guest-name-step" class="p-6 flex flex-col justify-center items-center flex-grow text-center space-y-4 bg-gray-50/50 dark:bg-navy-900/50">
            <div class="w-12 h-12 rounded-full bg-gold-100 dark:bg-gold-500/10 flex items-center justify-center text-gold-600 dark:text-gold-500">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
            </div>
            <h4 class="text-sm font-bold text-navy-900 dark:text-white uppercase tracking-wider">Comment vous appelez-vous ?</h4>
            <p class="text-xs text-gray-500 max-w-[240px]">Entrez votre nom pour démarrer une discussion avec notre équipe support.</p>
            <input type="text" id="chat-guest-name" placeholder="Votre nom" class="w-full text-xs text-center border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-lg py-3 focus:ring-gold-500 focus:border-gold-500" onkeyup="if(event.key === 'Enter') startGuestChat()">
            <button onclick="startGuestChat()" class="w-full bg-navy-900 dark:bg-gold-500 text-white dark:text-navy-900 rounded-lg py-3 text-[10px] font-bold uppercase tracking-widest hover:opacity-90 transition-all">Commencer</button>
        </div>
        @endif

        <!-- Chat Area (Messages & Input) -->
        <div id="chat-area" class="flex flex-col flex-grow overflow-hidden @if(!Auth::check()) hidden @endif">
            <!-- Messages List -->
            <div id="chat-messages-container" class="flex-grow p-6 overflow-y-auto space-y-4 bg-gray-50/30 dark:bg-navy-950/20">
                <!-- Welcome message -->
                <div class="flex items-start gap-2.5 max-w-[80%]">
                    <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gold-500/20 border border-gold-500/30 flex items-center justify-center text-[10px] font-bold text-gold-500">IT</div>
                    <div class="bg-white dark:bg-navy-800 border border-gray-100 dark:border-gray-800 rounded-2xl rounded-tl-none p-3 shadow-sm">
                        <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed">Bonjour ! Comment pouvons-nous vous aider aujourd'hui ?</p>
                        <span class="block text-[8px] text-gray-400 text-right mt-1 font-semibold">Support</span>
                    </div>
                </div>
            </div>

            <!-- Input Bar -->
            <div class="p-4 bg-white dark:bg-navy-900 border-t border-gray-100 dark:border-gray-800 flex items-center gap-2">
                <input type="text" id="chat-input-message" placeholder="Écrivez votre message..." class="flex-grow text-xs border-gray-200 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800 rounded-xl py-3 px-4 focus:ring-gold-500 focus:border-gold-500 focus:outline-none" onkeyup="if(event.key === 'Enter') sendChatMessage()">
                <button onclick="sendChatMessage()" class="w-10 h-10 bg-gold-500 hover:bg-gold-600 text-navy-900 rounded-xl flex items-center justify-center shadow-lg transition-all duration-200 focus:outline-none">
                    <svg class="w-4 h-4 transform rotate-90" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    /* Styling adjustments for smooth animations */
    #chat-window {
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1), opacity 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }
    .bottom-18 {
        bottom: 4.5rem;
    }
</style>

<script>
    let chatOpen = false;
    let pollInterval = null;
    let lastMessageCount = 0;
    let guestName = localStorage.getItem('chat_guest_name') || '';

    function toggleChatWindow() {
        const windowEl = document.getElementById('chat-window');
        const openIcon = document.getElementById('chat-icon-open');
        const closeIcon = document.getElementById('chat-icon-close');
        
        chatOpen = !chatOpen;
        
        if (chatOpen) {
            windowEl.classList.remove('hidden');
            // Allow layout/display to register first, then apply transition classes
            setTimeout(() => {
                windowEl.classList.remove('translate-y-4', 'scale-95', 'opacity-0');
            }, 10);
            
            openIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
            
            // Clear unread badge
            document.getElementById('chat-unread-badge').classList.add('hidden');

            // Scroll to bottom
            scrollToBottom();

            // Start polling
            fetchMessages();
            if (!pollInterval) {
                pollInterval = setInterval(fetchMessages, 4000);
            }
        } else {
            windowEl.classList.add('translate-y-4', 'scale-95', 'opacity-0');
            setTimeout(() => {
                windowEl.classList.add('hidden');
            }, 300);
            
            openIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');

            // Stop polling
            if (pollInterval) {
                clearInterval(pollInterval);
                pollInterval = null;
            }
        }
    }

    function startGuestChat() {
        const inputName = document.getElementById('chat-guest-name').value.trim();
        if (!inputName) {
            alert('Veuillez entrer votre nom.');
            return;
        }
        guestName = inputName;
        localStorage.setItem('chat_guest_name', guestName);

        document.getElementById('guest-name-step').classList.add('hidden');
        document.getElementById('chat-area').classList.remove('hidden');
        scrollToBottom();

        // Start polling
        fetchMessages();
        if (!pollInterval) {
            pollInterval = setInterval(fetchMessages, 4000);
        }
    }

    // Check if guest name already stored, show chat-area instead of name input
    document.addEventListener("DOMContentLoaded", function() {
        if (guestName && document.getElementById('guest-name-step')) {
            document.getElementById('guest-name-step').classList.add('hidden');
            document.getElementById('chat-area').classList.remove('hidden');
        }
    });

    function fetchMessages() {
        fetch('{{ route('chat.messages') }}')
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    renderMessages(data.messages);
                }
            })
            .catch(error => console.error('Error fetching chat messages:', error));
    }

    function renderMessages(messages) {
        const container = document.getElementById('chat-messages-container');
        
        // Save scroll position
        const isAtBottom = container.scrollHeight - container.scrollTop <= container.clientHeight + 40;

        // Keep the welcome message
        let html = `
            <div class="flex items-start gap-2.5 max-w-[80%]">
                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gold-500/20 border border-gold-500/30 flex items-center justify-center text-[10px] font-bold text-gold-500">IT</div>
                <div class="bg-white dark:bg-navy-800 border border-gray-100 dark:border-gray-800 rounded-2xl rounded-tl-none p-3 shadow-sm">
                    <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed">Bonjour ! Comment pouvons-nous vous aider aujourd'hui ?</p>
                    <span class="block text-[8px] text-gray-400 text-right mt-1 font-semibold">Support</span>
                </div>
            </div>
        `;

        messages.forEach(msg => {
            if (msg.is_from_admin) {
                html += `
                    <div class="flex items-start gap-2.5 max-w-[80%]">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gold-500/20 border border-gold-500/30 flex items-center justify-center text-[10px] font-bold text-gold-500">IT</div>
                        <div class="bg-white dark:bg-navy-800 border border-gray-100 dark:border-gray-800 rounded-2xl rounded-tl-none p-3 shadow-sm">
                            <p class="text-xs text-gray-800 dark:text-gray-200 leading-relaxed">${formatMessageText(msg.message)}</p>
                            <span class="block text-[8px] text-gray-400 text-right mt-1 font-semibold">${msg.time}</span>
                        </div>
                    </div>
                `;
            } else {
                html += `
                    <div class="flex items-start gap-2.5 max-w-[80%] ml-auto justify-end">
                        <div class="bg-navy-900 text-white rounded-2xl rounded-tr-none p-3 shadow-sm">
                            <p class="text-xs leading-relaxed">${formatMessageText(msg.message)}</p>
                            <span class="block text-[8px] text-gray-400 text-right mt-1 font-semibold">${msg.time}</span>
                        </div>
                    </div>
                `;
            }
        });

        container.innerHTML = html;

        // If we received new messages and the window is closed, show the unread badge
        if (messages.length > lastMessageCount) {
            const lastMsg = messages[messages.length - 1];
            if (!chatOpen && lastMsg.is_from_admin) {
                document.getElementById('chat-unread-badge').classList.remove('hidden');
            }
            lastMessageCount = messages.length;
            
            if (chatOpen || isAtBottom) {
                scrollToBottom();
            }
        }
    }

    function sendChatMessage() {
        const input = document.getElementById('chat-input-message');
        const text = input.value.trim();
        if (!text) return;

        input.value = '';

        // Append client message immediately for instant feedback
        const container = document.getElementById('chat-messages-container');
        const escapedText = escapeHtml(text);
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + now.getMinutes().toString().padStart(2, '0');

        const clientMessageHtml = `
            <div class="flex items-start gap-2.5 max-w-[80%] ml-auto justify-end">
                <div class="bg-navy-900 text-white rounded-2xl rounded-tr-none p-3 shadow-sm">
                    <p class="text-xs leading-relaxed">${escapedText}</p>
                    <span class="block text-[8px] text-gray-400 text-right mt-1 font-semibold">${timeStr}</span>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', clientMessageHtml);

        // Add typing indicator
        const typingIndicatorHtml = `
            <div id="chat-typing-indicator" class="flex items-start gap-2.5 max-w-[80%] animate-pulse">
                <div class="flex-shrink-0 w-6 h-6 rounded-full bg-gold-500/20 border border-gold-500/30 flex items-center justify-center text-[10px] font-bold text-gold-500">IT</div>
                <div class="bg-white dark:bg-navy-800 border border-gray-100 dark:border-gray-800 rounded-2xl rounded-tl-none p-3 shadow-sm">
                    <p class="text-xs text-gray-500 leading-relaxed italic">L'assistant IA écrit...</p>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', typingIndicatorHtml);
        scrollToBottom();

        // Temporarily disable input
        input.disabled = true;
        input.placeholder = "IA réfléchit...";

        const payload = {
            message: text
        };

        if (guestName) {
            payload.user_name = guestName;
        }

        fetch('{{ route('chat.send') }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            // Re-enable input
            input.disabled = false;
            input.placeholder = "Écrivez votre message...";
            input.focus();

            // Remove typing indicator
            const typingEl = document.getElementById('chat-typing-indicator');
            if (typingEl) {
                typingEl.remove();
            }

            if (data.success) {
                fetchMessages();
            }
        })
        .catch(error => {
            console.error('Error sending chat message:', error);
            input.disabled = false;
            input.placeholder = "Écrivez votre message...";
            const typingEl = document.getElementById('chat-typing-indicator');
            if (typingEl) {
                typingEl.remove();
            }
        });
    }

    function scrollToBottom() {
        const container = document.getElementById('chat-messages-container');
        if (container) {
            container.scrollTop = container.scrollHeight;
        }
    }

    function formatMessageText(text) {
        let formatted = escapeHtml(text);

        // Convert bold: **text** to <strong>text</strong>
        formatted = formatted.replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>');

        // Convert italic: *text* to <em>text</em>
        formatted = formatted.replace(/\*(.*?)\*/g, '<em>$1</em>');

        // Convert bullet points: starting with "- " or "* " on new lines
        const lines = formatted.split('\n');
        const processedLines = lines.map(line => {
            const trimmed = line.trim();
            if (trimmed.startsWith('- ') || trimmed.startsWith('* ')) {
                return `<li class="ml-4 list-disc">${trimmed.substring(2)}</li>`;
            }
            return line;
        });
        
        formatted = processedLines.join('<br>');
        
        // Clean up adjacent <br>s inside list tags
        formatted = formatted.replace(/(<\/li>)<br>(<li)/g, '$1$2');

        return formatted;
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
