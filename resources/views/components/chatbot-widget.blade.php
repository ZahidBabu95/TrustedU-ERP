{{-- ═══════════════════════ CHATBOT WIDGET — Gemini AI Powered ═══════════════════════ --}}
@if(\App\Models\SystemSetting::get('chatbot_enabled', true))
@php
    $botName = \App\Models\SystemSetting::get('chatbot_bot_name', 'TrustedU Assistant');
@endphp
<div id="trustedu-chatbot" x-data="chatBot()" x-cloak>

    {{-- ━━━ Floating Chat Button ━━━ --}}
    <button @click="toggle()"
            class="fixed bottom-6 right-6 z-[9999] w-[60px] h-[60px] rounded-full shadow-2xl
                   flex items-center justify-center transition-all duration-500 group"
            :class="isOpen
                ? 'bg-slate-800 rotate-0 scale-90'
                : 'bg-gradient-to-br from-blue-500 via-indigo-500 to-violet-600 hover:from-blue-400 hover:via-indigo-400 hover:to-violet-500 hover:scale-110'"
            :style="!isOpen ? 'box-shadow: 0 8px 32px rgba(99,102,241,.45), 0 0 0 0 rgba(99,102,241,.4); animation: chatPulse 2.5s ease-in-out infinite;' : ''">

        {{-- Chat icon --}}
        <svg x-show="!isOpen" class="w-7 h-7 text-white drop-shadow" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
        </svg>

        {{-- Close icon --}}
        <svg x-show="isOpen" class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
        </svg>

        {{-- Notification badge --}}
        <span x-show="unreadCount > 0 && !isOpen"
              x-text="unreadCount"
              class="absolute -top-1 -right-1 w-5 h-5 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center animate-bounce"></span>

        {{-- Gemini sparkle effect --}}
        <span x-show="!isOpen" class="absolute -top-1 -left-1 w-4 h-4" style="animation: sparkle 3s ease-in-out infinite;">
            <svg viewBox="0 0 24 24" fill="none" class="w-4 h-4 text-yellow-300 drop-shadow-sm">
                <path d="M12 2L14.09 8.26L20 9.27L15.55 13.97L16.91 20L12 16.9L7.09 20L8.45 13.97L4 9.27L9.91 8.26L12 2Z" fill="currentColor"/>
            </svg>
        </span>
    </button>

    {{-- ━━━ Chat Window ━━━ --}}
    <div x-show="isOpen"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-4 scale-95"
         class="fixed bottom-24 right-6 z-[9998] w-[390px] max-w-[calc(100vw-2rem)]
                bg-white rounded-2xl shadow-2xl overflow-hidden border border-slate-200/50
                flex flex-col"
         style="height: 580px; box-shadow: 0 25px 65px -5px rgba(0,0,0,.18), 0 10px 30px -10px rgba(0,0,0,.12);">

        {{-- ── Header ── --}}
        <div class="relative overflow-hidden px-5 py-4 flex items-center gap-3 shrink-0"
             style="background: linear-gradient(135deg, #4338ca 0%, #6366f1 40%, #818cf8 100%);">
            {{-- Subtle pattern overlay --}}
            <div class="absolute inset-0 opacity-10"
                 style="background-image: radial-gradient(circle at 20% 80%, white 1px, transparent 1px), radial-gradient(circle at 80% 20%, white 1px, transparent 1px); background-size: 30px 30px;"></div>

            <div class="relative flex items-center gap-3 flex-1 min-w-0">
                <div class="relative">
                    <div class="w-11 h-11 rounded-full bg-white/15 backdrop-blur-sm flex items-center justify-center ring-2 ring-white/20">
                        <img src="{{ asset('images/logo/favicon.png') }}" alt="TrustedU" class="w-6 h-6 rounded">
                    </div>
                    <span class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 bg-green-400 rounded-full border-[2.5px] border-indigo-600" style="animation: onlinePulse 2s ease-in-out infinite;"></span>
                </div>
                <div>
                    <h3 class="text-white font-semibold text-[14px] tracking-tight">{{ $botName }}</h3>
                    <p class="text-indigo-200 text-[11px] flex items-center gap-1.5 mt-0.5">
                        <span class="w-1.5 h-1.5 bg-green-400 rounded-full inline-block"></span>
                        <span x-text="isTyping ? 'Thinking...' : 'Online • AI Powered'"></span>
                    </p>
                </div>
            </div>

            {{-- Header actions --}}
            <div class="relative flex items-center gap-1">
                <button @click="clearChat()" class="text-white/50 hover:text-white/90 transition p-1.5 rounded-lg hover:bg-white/10" title="Clear chat">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4a1 1 0 011 1v1H9V4a1 1 0 011-1z"/>
                    </svg>
                </button>
                <button @click="isOpen = false" class="text-white/50 hover:text-white/90 transition p-1.5 rounded-lg hover:bg-white/10" title="Minimize">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- ── Messages Area ── --}}
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-3" x-ref="messagesContainer"
             style="background: linear-gradient(180deg, #f5f7ff 0%, #ffffff 40%, #fafbff 100%);">

            {{-- Welcome message --}}
            <template x-if="messages.length === 0 && greeting">
                <div class="flex gap-2.5 items-start animate-fadeIn">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shrink-0 mt-0.5 shadow-sm shadow-indigo-200">
                        <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none">
                            <path d="M12 2L14.09 8.26L20 9.27L15.55 13.97L16.91 20L12 16.9L7.09 20L8.45 13.97L4 9.27L9.91 8.26L12 2Z" fill="currentColor" opacity="0.9"/>
                        </svg>
                    </div>
                    <div class="bg-white rounded-2xl rounded-tl-md px-4 py-3 shadow-sm border border-indigo-50 max-w-[85%]">
                        <p class="text-[13px] text-slate-700 leading-relaxed whitespace-pre-line" x-text="greeting"></p>
                    </div>
                </div>
            </template>

            {{-- Chat messages --}}
            <template x-for="(msg, index) in messages" :key="index">
                <div :class="msg.role === 'user' ? 'flex justify-end animate-slideRight' : 'flex gap-2.5 items-start animate-slideLeft'">
                    {{-- Bot avatar --}}
                    <template x-if="msg.role !== 'user'">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shrink-0 mt-0.5 shadow-sm shadow-indigo-200">
                            <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none">
                                <path d="M12 2L14.09 8.26L20 9.27L15.55 13.97L16.91 20L12 16.9L7.09 20L8.45 13.97L4 9.27L9.91 8.26L12 2Z" fill="currentColor" opacity="0.9"/>
                            </svg>
                        </div>
                    </template>

                    <div :class="msg.role === 'user'
                        ? 'bg-gradient-to-br from-indigo-600 to-violet-600 text-white rounded-2xl rounded-tr-sm px-4 py-2.5 max-w-[80%] shadow-md shadow-indigo-100'
                        : 'bg-white rounded-2xl rounded-tl-sm px-4 py-2.5 shadow-sm border border-indigo-50 max-w-[85%]'">
                        <p class="text-[13px] leading-relaxed whitespace-pre-line"
                           :class="msg.role === 'user' ? 'text-white' : 'text-slate-700'"
                           x-text="msg.message"></p>
                        <p class="text-[10px] mt-1 flex items-center gap-1"
                           :class="msg.role === 'user' ? 'text-indigo-200' : 'text-slate-400'">
                            <span x-text="msg.time"></span>
                            <template x-if="msg.role !== 'user'">
                                <span class="inline-flex items-center gap-0.5 ml-1 text-indigo-400">
                                    <svg class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2L14.09 8.26L20 9.27L15.55 13.97L16.91 20L12 16.9L7.09 20L8.45 13.97L4 9.27L9.91 8.26L12 2Z"/></svg>
                                    AI
                                </span>
                            </template>
                        </p>
                    </div>
                </div>
            </template>

            {{-- Typing indicator --}}
            <div x-show="isTyping" class="flex gap-2.5 items-start animate-fadeIn">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shrink-0 shadow-sm shadow-indigo-200">
                    <svg class="w-4 h-4 text-white" viewBox="0 0 24 24" fill="none" style="animation: geminiSpin 2s linear infinite;">
                        <path d="M12 2L14.09 8.26L20 9.27L15.55 13.97L16.91 20L12 16.9L7.09 20L8.45 13.97L4 9.27L9.91 8.26L12 2Z" fill="currentColor" opacity="0.9"/>
                    </svg>
                </div>
                <div class="bg-white rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm border border-indigo-50">
                    <div class="flex gap-1.5 items-center">
                        <span class="w-2 h-2 bg-indigo-300 rounded-full" style="animation: typingDot 1.4s ease-in-out infinite;"></span>
                        <span class="w-2 h-2 bg-indigo-400 rounded-full" style="animation: typingDot 1.4s ease-in-out infinite 0.2s;"></span>
                        <span class="w-2 h-2 bg-indigo-500 rounded-full" style="animation: typingDot 1.4s ease-in-out infinite 0.4s;"></span>
                        <span class="text-[10px] text-indigo-400 ml-1.5">AI thinking...</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Quick Actions ── --}}
        <div x-show="messages.length === 0" class="px-4 pb-2 shrink-0 bg-white/80">
            <div class="flex flex-wrap gap-1.5">
                <button @click="sendQuickMessage('মডিউল ও ফিচার সম্পর্কে জানতে চাই')"
                        class="text-[11px] font-medium px-3 py-1.5 bg-indigo-50 text-indigo-700 rounded-full hover:bg-indigo-100 transition border border-indigo-100 hover:shadow-sm">
                    📋 মডিউল ও ফিচার
                </button>
                <button @click="sendQuickMessage('কীভাবে ডেমো বুক করব?')"
                        class="text-[11px] font-medium px-3 py-1.5 bg-violet-50 text-violet-700 rounded-full hover:bg-violet-100 transition border border-violet-100 hover:shadow-sm">
                    🎯 ডেমো বুকিং
                </button>
                <button @click="sendQuickMessage('প্রাইসিং সম্পর্কে জানতে চাই')"
                        class="text-[11px] font-medium px-3 py-1.5 bg-blue-50 text-blue-700 rounded-full hover:bg-blue-100 transition border border-blue-100 hover:shadow-sm">
                    💰 প্রাইসিং
                </button>
                <button @click="sendQuickMessage('যোগাযোগের তথ্য দিন')"
                        class="text-[11px] font-medium px-3 py-1.5 bg-emerald-50 text-emerald-700 rounded-full hover:bg-emerald-100 transition border border-emerald-100 hover:shadow-sm">
                    📞 যোগাযোগ
                </button>
            </div>
        </div>

        {{-- ── Input Area ── --}}
        <div class="border-t border-slate-100 px-4 py-3 bg-white shrink-0">
            <form @submit.prevent="sendMessage()" class="flex items-center gap-2">
                <div class="flex-1 relative">
                    <input x-model="newMessage"
                           x-ref="chatInput"
                           type="text"
                           maxlength="1000"
                           placeholder="আপনার প্রশ্ন লিখুন..."
                           :disabled="isTyping"
                           class="w-full px-4 py-2.5 bg-slate-50/80 border border-slate-200 rounded-xl text-[13px]
                                  text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2
                                  focus:ring-indigo-500/20 focus:border-indigo-400 transition-all
                                  disabled:opacity-50 disabled:cursor-not-allowed">
                </div>
                <button type="submit"
                        :disabled="!newMessage.trim() || isTyping"
                        class="w-10 h-10 bg-gradient-to-br from-indigo-600 to-violet-600 rounded-xl
                               flex items-center justify-center text-white shrink-0
                               hover:from-indigo-500 hover:to-violet-500 disabled:opacity-40
                               disabled:cursor-not-allowed transition-all active:scale-95
                               shadow-sm hover:shadow-md hover:shadow-indigo-500/20">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                              d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </form>
            {{-- ── Powered by Gemini ── --}}
            <div class="flex items-center justify-center gap-1.5 mt-2">
                <span class="text-[10px] text-slate-400">Powered by</span>
                <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none">
                    <path d="M12 2L14.09 8.26L20 9.27L15.55 13.97L16.91 20L12 16.9L7.09 20L8.45 13.97L4 9.27L9.91 8.26L12 2Z"
                          fill="url(#geminiGrad)"/>
                    <defs>
                        <linearGradient id="geminiGrad" x1="4" y1="2" x2="20" y2="20">
                            <stop stop-color="#4285F4"/><stop offset="0.5" stop-color="#9B72CB"/><stop offset="1" stop-color="#D96570"/>
                        </linearGradient>
                    </defs>
                </svg>
                <span class="text-[10px] font-semibold bg-gradient-to-r from-blue-500 via-purple-500 to-pink-500 bg-clip-text text-transparent">Gemini AI</span>
            </div>
        </div>
    </div>
</div>

<style>
    @keyframes chatPulse {
        0%, 100% { box-shadow: 0 8px 32px rgba(99,102,241,.45), 0 0 0 0 rgba(99,102,241,.4); }
        50% { box-shadow: 0 8px 32px rgba(99,102,241,.45), 0 0 0 14px rgba(99,102,241,0); }
    }
    @keyframes typingDot {
        0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
        30% { transform: translateY(-5px); opacity: 1; }
    }
    @keyframes onlinePulse {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(74,222,128,.5); }
        50% { opacity: .7; box-shadow: 0 0 0 4px rgba(74,222,128,0); }
    }
    @keyframes sparkle {
        0%, 100% { opacity: 1; transform: scale(1) rotate(0deg); }
        50% { opacity: .4; transform: scale(.7) rotate(180deg); }
    }
    @keyframes geminiSpin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes slideLeft {
        from { opacity: 0; transform: translateX(-10px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideRight {
        from { opacity: 0; transform: translateX(10px); }
        to { opacity: 1; transform: translateX(0); }
    }
    .animate-fadeIn { animation: fadeIn .4s ease-out; }
    .animate-slideLeft { animation: slideLeft .3s ease-out; }
    .animate-slideRight { animation: slideRight .3s ease-out; }
    [x-cloak] { display: none !important; }

    /* Premium scrollbar */
    #chat-messages::-webkit-scrollbar { width: 4px; }
    #chat-messages::-webkit-scrollbar-track { background: transparent; }
    #chat-messages::-webkit-scrollbar-thumb { background: linear-gradient(180deg, #c7d2fe, #a5b4fc); border-radius: 10px; }
    #chat-messages::-webkit-scrollbar-thumb:hover { background: #818cf8; }
</style>

<script>
function chatBot() {
    return {
        isOpen: false,
        isTyping: false,
        newMessage: '',
        messages: [],
        greeting: null,
        sessionId: null,
        conversationId: null,
        unreadCount: 0,
        initialized: false,

        toggle() {
            this.isOpen = !this.isOpen;
            if (this.isOpen) {
                this.unreadCount = 0;
                if (!this.initialized) {
                    this.initChat();
                }
                this.$nextTick(() => {
                    this.$refs.chatInput?.focus();
                    this.scrollToBottom();
                });
            }
        },

        async initChat() {
            this.sessionId = localStorage.getItem('trustedu_chat_session');

            try {
                const res = await fetch('/chatbot/start', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ session_id: this.sessionId }),
                });

                const data = await res.json();
                this.sessionId = data.session_id;
                this.conversationId = data.conversation_id;
                this.greeting = data.greeting;

                localStorage.setItem('trustedu_chat_session', this.sessionId);

                if (data.messages && data.messages.length > 0) {
                    this.messages = data.messages;
                    this.greeting = null;
                }

                this.initialized = true;
                this.$nextTick(() => this.scrollToBottom());
            } catch (e) {
                console.error('Chat init error:', e);
                this.greeting = "স্বাগতম! চ্যাট সার্ভারে সংযোগ স্থাপন করা যাচ্ছে না। কিছুক্ষণ পর আবার চেষ্টা করুন।";
                this.initialized = true;
            }
        },

        async sendMessage() {
            const msg = this.newMessage.trim();
            if (!msg || this.isTyping) return;

            this.newMessage = '';
            this.greeting = null;

            this.messages.push({
                role: 'user',
                message: msg,
                time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
            });

            this.$nextTick(() => this.scrollToBottom());
            this.isTyping = true;

            try {
                const res = await fetch('/chatbot/message', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({
                        session_id: this.sessionId,
                        message: msg,
                    }),
                });

                const data = await res.json();

                if (data.bot_response) {
                    this.messages.push(data.bot_response);
                    if (!this.isOpen) {
                        this.unreadCount++;
                    }
                }
            } catch (e) {
                console.error('Chat send error:', e);
                this.messages.push({
                    role: 'bot',
                    message: 'দুঃখিত, একটি ত্রুটি হয়েছে। অনুগ্রহ করে আবার চেষ্টা করুন। 🙏',
                    time: new Date().toLocaleTimeString('en-US', { hour: '2-digit', minute: '2-digit' }),
                });
            } finally {
                this.isTyping = false;
                this.$nextTick(() => {
                    this.scrollToBottom();
                    this.$refs.chatInput?.focus();
                });
            }
        },

        sendQuickMessage(msg) {
            this.newMessage = msg;
            this.sendMessage();
        },

        clearChat() {
            if (this.messages.length === 0) return;
            this.messages = [];
            this.greeting = null;
            localStorage.removeItem('trustedu_chat_session');
            this.initialized = false;
            this.initChat();
        },

        scrollToBottom() {
            const container = this.$refs.messagesContainer;
            if (container) {
                container.scrollTop = container.scrollHeight;
            }
        },
    };
}
</script>
@endif
