<x-filament-panels::page>
    <style>
        /* ━━━ Support Ticket Premium Styles ━━━ */
        .st-layout { display: grid; grid-template-columns: 1fr 320px; gap: 0; min-height: calc(100vh - 180px); }
        @media (max-width: 1024px) { .st-layout { grid-template-columns: 1fr; } }

        /* ── Thread Area ── */
        .st-thread { display: flex; flex-direction: column; position: relative; }
        .st-messages { flex: 1; overflow-y: auto; padding: 2rem; padding-bottom: 7rem; space-y: 1.5rem; }

        .st-system-msg { display: flex; justify-content: center; padding: 0.5rem 0; }
        .st-system-badge { padding: 0.375rem 1rem; background: rgba(148,163,184,0.1); color: #94a3b8; font-size: 11px; font-weight: 700; border-radius: 9999px; text-transform: uppercase; letter-spacing: 0.1em; }

        .st-msg-row { display: flex; gap: 1rem; max-width: 720px; margin-bottom: 1.5rem; }
        .st-msg-row.sent { margin-left: auto; flex-direction: row-reverse; }

        .st-msg-avatar { width: 40px; height: 40px; border-radius: 0.75rem; object-fit: cover; flex-shrink: 0; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .st-msg-avatar.agent { border: 2px solid #e0e7ff; }

        .st-msg-bubble {
            padding: 1.25rem; border-radius: 1rem; font-size: 0.875rem; line-height: 1.75;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .st-msg-bubble.client { background: white; border: 1px solid rgba(0,0,0,0.06); border-top-left-radius: 0.25rem; color: #334155; }
        .dark .st-msg-bubble.client { background: rgb(17 24 39); border-color: rgb(31 41 55); color: #e2e8f0; }
        .st-msg-bubble.agent { background: #4f46e5; color: white; border-top-right-radius: 0.25rem; box-shadow: 0 4px 12px rgba(79,70,229,0.2); }
        .st-msg-bubble.internal { background: #fffbeb; border: 2px dashed #fbbf24; color: #92400e; border-radius: 0.75rem; }
        .dark .st-msg-bubble.internal { background: rgba(251,191,36,0.08); border-color: #92400e; color: #fde68a; }

        .st-msg-sender { font-weight: 700; font-size: 0.875rem; }
        .st-msg-time { font-size: 0.75rem; color: #94a3b8; }
        .st-msg-meta { display: flex; align-items: baseline; gap: 0.75rem; margin-bottom: 0.5rem; }
        .st-msg-meta.sent { justify-content: flex-end; }

        .st-msg-attachment { display: inline-flex; align-items: center; gap: 0.5rem; padding: 0.5rem 0.75rem; border-radius: 0.5rem; background: rgba(0,0,0,0.05); margin-top: 0.75rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: background 0.2s; }
        .st-msg-attachment:hover { background: rgba(0,0,0,0.08); }

        .st-internal-badge { display: inline-flex; align-items: center; gap: 0.35rem; font-size: 11px; font-weight: 700; color: #b45309; text-transform: uppercase; margin-bottom: 0.5rem; letter-spacing: 0.05em; }

        /* ── Quick Reply ── */
        .st-quick-reply {
            position: absolute; bottom: 1.5rem; left: 2rem; right: 2rem;
            background: rgba(255,255,255,0.92); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(99,102,241,0.15); box-shadow: 0 8px 32px rgba(0,0,0,0.08);
            border-radius: 1rem; padding: 0.5rem; display: flex; align-items: center; gap: 0.5rem;
        }
        .dark .st-quick-reply { background: rgba(17,24,39,0.92); border-color: rgba(99,102,241,0.2); }
        .st-quick-reply input { flex: 1; border: none; background: transparent; padding: 0.75rem 1rem; font-size: 0.875rem; outline: none; color: #334155; }
        .dark .st-quick-reply input { color: #e2e8f0; }
        .st-quick-reply input::placeholder { color: #94a3b8; }
        .st-reply-btn { background: #4f46e5; color: white; border: none; padding: 0.625rem; border-radius: 0.75rem; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all 0.2s; box-shadow: 0 2px 8px rgba(79,70,229,0.3); }
        .st-reply-btn:hover { transform: scale(1.05); }
        .st-reply-btn:active { transform: scale(0.95); }
        .st-action-btn { background: none; border: none; color: #94a3b8; padding: 0.5rem; border-radius: 0.5rem; cursor: pointer; transition: all 0.2s; display: flex; align-items: center; }
        .st-action-btn:hover { background: rgba(99,102,241,0.06); color: #4f46e5; }

        /* ── Sidebar ── */
        .st-sidebar { background: white; border-left: 1px solid rgba(0,0,0,0.06); display: flex; flex-direction: column; overflow-y: auto; }
        .dark .st-sidebar { background: rgb(17 24 39); border-color: rgb(31 41 55); }

        .st-sidebar-section { padding: 1.25rem; border-bottom: 1px solid #f1f5f9; }
        .dark .st-sidebar-section { border-color: rgb(31 41 55); }
        .st-sidebar-title { font-size: 10px; font-weight: 800; color: #94a3b8; text-transform: uppercase; letter-spacing: 0.2em; margin-bottom: 1rem; }

        .st-prop-row { display: flex; justify-content: space-between; align-items: center; padding: 0.4rem 0; }
        .st-prop-label { font-size: 0.875rem; font-weight: 500; color: #64748b; }
        .st-prop-badge { display: inline-flex; align-items: center; gap: 0.35rem; padding: 0.25rem 0.75rem; border-radius: 0.5rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; cursor: pointer; }

        .st-customer-card { display: flex; align-items: center; gap: 0.75rem; margin-bottom: 1rem; }
        .st-customer-avatar { width: 48px; height: 48px; border-radius: 0.75rem; object-fit: cover; }
        .st-customer-name { font-size: 0.875rem; font-weight: 700; color: #0f172a; }
        .dark .st-customer-name { color: #f8fafc; }
        .st-customer-sub { font-size: 0.75rem; color: #94a3b8; }
        .st-contact-row { display: flex; align-items: center; gap: 0.75rem; color: #475569; font-size: 0.75rem; font-weight: 500; padding: 0.4rem 0; }

        /* ── Timeline ── */
        .st-timeline { position: relative; padding-left: 1.75rem; }
        .st-timeline::before { content: ''; position: absolute; left: 0.45rem; top: 0.5rem; bottom: 0.5rem; width: 1px; background: #e2e8f0; }
        .dark .st-timeline::before { background: #374151; }
        .st-timeline-item { position: relative; padding-bottom: 1.25rem; }
        .st-timeline-dot { position: absolute; left: -1.35rem; top: 0.25rem; width: 14px; height: 14px; border-radius: 50%; border: 3px solid white; box-shadow: 0 1px 2px rgba(0,0,0,0.06); }
        .dark .st-timeline-dot { border-color: rgb(17 24 39); }
        .st-timeline-text { font-size: 11px; font-weight: 700; color: #1e293b; }
        .dark .st-timeline-text { color: #e2e8f0; }
        .st-timeline-date { font-size: 10px; color: #94a3b8; margin-top: 2px; }

        .st-change-status-btn {
            width: 100%; display: flex; align-items: center; justify-content: space-between;
            padding: 0.5rem 1rem; border: 1px solid #e2e8f0; border-radius: 0.5rem; background: white;
            font-size: 0.75rem; font-weight: 700; color: #475569; cursor: pointer; transition: all 0.2s;
        }
        .st-change-status-btn:hover { background: #f8fafc; }
        .dark .st-change-status-btn { background: rgb(17 24 39); border-color: rgb(31 41 55); color: #94a3b8; }

        /* Status dropdowns */
        .st-status-dropdown { display: none; position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #e2e8f0; border-radius: 0.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); z-index: 10; margin-top: 0.25rem; overflow: hidden; }
        .st-status-dropdown.show { display: block; }
        .dark .st-status-dropdown { background: rgb(17 24 39); border-color: rgb(31 41 55); }
        .st-status-option { padding: 0.5rem 0.75rem; font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: background 0.15s; }
        .st-status-option:hover { background: #f1f5f9; }
        .dark .st-status-option:hover { background: rgb(31 41 55); }
    </style>

    <div class="st-layout" style="border-radius: 0.75rem; overflow: hidden; border: 1px solid rgba(0,0,0,0.06);">
        {{-- ═══ LEFT: Message Thread ═══ --}}
        <div class="st-thread">
            <div class="st-messages">
                {{-- System: Ticket Created --}}
                <div class="st-system-msg" style="margin-bottom: 1.5rem;">
                    <span class="st-system-badge">Ticket Created • {{ $record->created_at->format('M d, h:i A') }}</span>
                </div>

                {{-- Description as first message --}}
                @if($record->description)
                    <div class="st-msg-row">
                        <div style="width: 40px; height: 40px; border-radius: 0.75rem; background: linear-gradient(135deg, #6366f1, #4f46e5); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 700; flex-shrink: 0;">
                            {{ strtoupper(substr($record->client?->name ?? $record->user?->name ?? 'U', 0, 2)) }}
                        </div>
                        <div style="flex: 1;">
                            <div class="st-msg-meta">
                                <span class="st-msg-sender" style="color: #0f172a;">{{ $record->client?->name ?? $record->user?->name ?? 'Unknown' }}</span>
                                <span class="st-msg-time">{{ $record->created_at->format('h:i A') }}</span>
                            </div>
                            <div class="st-msg-bubble client">
                                {!! nl2br(e($record->description)) !!}
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Message Thread --}}
                @foreach($record->messages as $message)
                    @if($message->isSystemMessage())
                        <div class="st-system-msg" style="margin-bottom: 1.5rem;">
                            <span class="st-system-badge">{{ $message->message }} • {{ $message->created_at->format('h:i A') }}</span>
                        </div>
                    @elseif($message->is_internal)
                        {{-- Internal Note --}}
                        <div style="display: flex; justify-content: center; margin-bottom: 1.5rem;">
                            <div style="max-width: 600px; width: 100;">
                                <div class="st-msg-bubble internal" style="display: flex; gap: 1rem; align-items: flex-start;">
                                    <x-heroicon-o-lock-closed style="width: 20px; height: 20px; color: #b45309; flex-shrink: 0; margin-top: 2px;" />
                                    <div style="flex: 1;">
                                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.35rem;">
                                            <span class="st-internal-badge">Internal Note</span>
                                            <span class="st-msg-time">{{ $message->created_at->format('h:i A') }}</span>
                                        </div>
                                        <p style="font-size: 0.875rem; line-height: 1.6; font-style: italic;">{{ $message->message }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @elseif($message->isFromAgent())
                        {{-- Agent Reply --}}
                        <div class="st-msg-row sent">
                            <div style="width: 40px; height: 40px; border-radius: 0.75rem; background: linear-gradient(135deg, #818cf8, #4f46e5); border: 2px solid #e0e7ff; display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 700; flex-shrink: 0;">
                                {{ strtoupper(substr($message->sender?->name ?? 'A', 0, 2)) }}
                            </div>
                            <div style="flex: 1;">
                                <div class="st-msg-meta sent">
                                    <span class="st-msg-time">{{ $message->created_at->format('h:i A') }}</span>
                                    <span class="st-msg-sender" style="color: #4f46e5;">{{ $message->sender?->name ?? 'Agent' }}</span>
                                </div>
                                <div class="st-msg-bubble agent">
                                    <p style="white-space: pre-wrap;">{{ $message->message }}</p>
                                    @if($message->attachment)
                                        <a href="{{ Storage::url($message->attachment) }}" target="_blank" class="st-msg-attachment" style="color: rgba(255,255,255,0.8);">
                                            <x-heroicon-o-paper-clip style="width: 14px; height: 14px;" />
                                            Attachment
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        {{-- Client Message --}}
                        <div class="st-msg-row">
                            <div style="width: 40px; height: 40px; border-radius: 0.75rem; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-size: 12px; font-weight: 700; flex-shrink: 0;">
                                {{ strtoupper(substr($message->sender?->name ?? 'C', 0, 2)) }}
                            </div>
                            <div style="flex: 1;">
                                <div class="st-msg-meta">
                                    <span class="st-msg-sender" style="color: #0f172a;">{{ $message->sender?->name ?? 'Client' }}</span>
                                    <span class="st-msg-time">{{ $message->created_at->format('h:i A') }}</span>
                                </div>
                                <div class="st-msg-bubble client">
                                    <p style="white-space: pre-wrap;">{{ $message->message }}</p>
                                    @if($message->attachment)
                                        <a href="{{ Storage::url($message->attachment) }}" target="_blank" class="st-msg-attachment">
                                            <x-heroicon-o-document style="width: 14px; height: 14px; color: #4f46e5;" />
                                            <span style="color: #475569;">Attachment</span>
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach

                @if($record->messages->isEmpty() && !$record->description)
                    <div style="text-align: center; padding: 4rem 2rem;">
                        <x-heroicon-o-chat-bubble-left-right style="width: 3rem; height: 3rem; margin: 0 auto 1rem; color: #e2e8f0;" />
                        <p style="font-size: 0.875rem; color: #94a3b8;">No messages yet. Send the first reply below.</p>
                    </div>
                @endif
            </div>

            {{-- ── Floating Quick Reply ── --}}
            @if($record->status !== 'closed')
                <div class="st-quick-reply">
                    <form wire:submit.prevent="sendReply" style="display: flex; align-items: center; gap: 0.5rem; flex: 1;">
                        <input type="text" wire:model="replyMessage" placeholder="Type a reply..." />
                        <label class="st-action-btn" style="position: relative;">
                            <input type="file" wire:model="replyAttachment" style="display: none;" />
                            <x-heroicon-o-paper-clip style="width: 20px; height: 20px;" />
                        </label>
                        <label class="st-action-btn" title="Internal Note" style="position: relative;">
                            <input type="checkbox" wire:model="isInternal" style="display: none;" />
                            <x-heroicon-o-lock-closed style="width: 20px; height: 20px; {{ $isInternal ? 'color: #f59e0b;' : '' }}" />
                        </label>
                        <button type="submit" class="st-reply-btn" wire:loading.attr="disabled">
                            <x-heroicon-s-paper-airplane style="width: 18px; height: 18px;" />
                        </button>
                    </form>
                </div>
            @else
                <div style="position: absolute; bottom: 1.5rem; left: 2rem; right: 2rem; background: #f8fafc; border-radius: 1rem; padding: 1.25rem; text-align: center;">
                    <x-heroicon-o-lock-closed style="width: 1.25rem; height: 1.25rem; margin: 0 auto 0.5rem; color: #94a3b8;" />
                    <p style="font-size: 0.875rem; color: #94a3b8;">Ticket is closed. Reopen to reply.</p>
                </div>
            @endif
        </div>

        {{-- ═══ RIGHT: Sidebar ═══ --}}
        <div class="st-sidebar">
            {{-- Properties --}}
            <div class="st-sidebar-section">
                <h3 class="st-sidebar-title">Properties</h3>
                <div style="display: flex; flex-direction: column; gap: 0.75rem;">
                    {{-- Status --}}
                    <div class="st-prop-row">
                        <span class="st-prop-label">Status</span>
                        <div style="position: relative;" x-data="{ open: false }">
                            <button @click="open = !open" class="st-prop-badge"
                                    style="background: {{ $record->status_color }}15; color: {{ $record->status_color }};">
                                {{ \App\Models\SupportTicket::STATUS_LABELS[$record->status] ?? $record->status }}
                                <x-heroicon-o-chevron-down style="width: 12px; height: 12px;" />
                            </button>
                            <div class="st-status-dropdown" x-show="open" @click.outside="open = false" x-cloak>
                                @foreach(\App\Models\SupportTicket::STATUS_LABELS as $val => $label)
                                    <div class="st-status-option" wire:click="updateStatus('{{ $val }}')" @click="open = false"
                                         style="color: {{ \App\Models\SupportTicket::STATUS_COLORS[$val] }};">
                                        {{ $label }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Priority --}}
                    <div class="st-prop-row">
                        <span class="st-prop-label">Priority</span>
                        <div style="position: relative;" x-data="{ open: false }">
                            <button @click="open = !open" class="st-prop-badge"
                                    style="background: {{ $record->priority_color }}15; color: {{ $record->priority_color }};">
                                @if($record->priority === 'urgent' || $record->priority === 'high')
                                    <x-heroicon-s-exclamation-triangle style="width: 12px; height: 12px;" />
                                @endif
                                {{ \App\Models\SupportTicket::PRIORITY_LABELS[$record->priority] ?? $record->priority }}
                                <x-heroicon-o-chevron-down style="width: 12px; height: 12px;" />
                            </button>
                            <div class="st-status-dropdown" x-show="open" @click.outside="open = false" x-cloak>
                                @foreach(\App\Models\SupportTicket::PRIORITY_LABELS as $val => $label)
                                    <div class="st-status-option" wire:click="updatePriority('{{ $val }}')" @click="open = false"
                                         style="color: {{ \App\Models\SupportTicket::PRIORITY_COLORS[$val] }};">
                                        {{ $label }}
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Assigned --}}
                    <div class="st-prop-row">
                        <span class="st-prop-label">Assigned</span>
                        <div style="display: flex; align-items: center; gap: 0.5rem;">
                            <span style="font-size: 0.75rem; font-weight: 700; color: #334155;">{{ $record->assignee?->name ?? 'Unassigned' }}</span>
                            @if($record->assignee)
                                <div style="width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #4f46e5); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: white;">
                                    {{ strtoupper(substr($record->assignee->name, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Category --}}
                    @if($record->category)
                        <div class="st-prop-row">
                            <span class="st-prop-label">Category</span>
                            <span style="font-size: 0.75rem; font-weight: 600; color: #334155;">
                                {{ \App\Models\SupportTicket::CATEGORY_LABELS[$record->category] ?? ucfirst($record->category) }}
                            </span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Customer Info --}}
            <div class="st-sidebar-section">
                <h3 class="st-sidebar-title">Customer Info</h3>
                @if($record->client)
                    <div class="st-customer-card">
                        <div style="width: 48px; height: 48px; border-radius: 0.75rem; background: linear-gradient(135deg, #10b981, #059669); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; font-weight: 700; flex-shrink: 0;">
                            {{ strtoupper(substr($record->client->name, 0, 2)) }}
                        </div>
                        <div>
                            <p class="st-customer-name">{{ $record->client->name }}</p>
                            <p class="st-customer-sub">{{ $record->client->institution_type ? ucfirst(str_replace('_', ' ', $record->client->institution_type)) : 'Client' }}</p>
                        </div>
                    </div>
                    @if($record->client->phone)
                        <div class="st-contact-row">
                            <x-heroicon-o-phone style="width: 16px; height: 16px; opacity: 0.6;" />
                            {{ $record->client->phone }}
                        </div>
                    @endif
                    @if($record->client->email)
                        <div class="st-contact-row">
                            <x-heroicon-o-envelope style="width: 16px; height: 16px; opacity: 0.6;" />
                            {{ $record->client->email }}
                        </div>
                    @endif
                    @if($record->client->district)
                        <div class="st-contact-row">
                            <x-heroicon-o-map-pin style="width: 16px; height: 16px; opacity: 0.6;" />
                            {{ $record->client->district }}
                        </div>
                    @endif
                @elseif($record->email)
                    <div class="st-contact-row">
                        <x-heroicon-o-envelope style="width: 16px; height: 16px; opacity: 0.6;" />
                        {{ $record->email }}
                    </div>
                @else
                    <p style="font-size: 0.8rem; color: #94a3b8;">No client linked</p>
                @endif
            </div>

            {{-- Labels --}}
            @if($record->labels && count($record->labels) > 0)
                <div class="st-sidebar-section">
                    <h3 class="st-sidebar-title">Labels</h3>
                    <div style="display: flex; flex-wrap: wrap; gap: 0.5rem;">
                        @foreach($record->labels as $label)
                            <span style="padding: 0.25rem 0.75rem; background: #f1f5f9; color: #334155; font-size: 0.75rem; font-weight: 700; border-radius: 9999px;">{{ $label }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- History / Timeline --}}
            <div class="st-sidebar-section" style="flex: 1; background: rgba(248,250,252,0.5);">
                <h3 class="st-sidebar-title">History</h3>
                <div class="st-timeline">
                    {{-- Created --}}
                    <div class="st-timeline-item">
                        <div class="st-timeline-dot" style="background: #94a3b8;"></div>
                        <p class="st-timeline-text">Ticket created</p>
                        <p class="st-timeline-date">{{ $record->created_at->format('M d, h:i A') }}</p>
                    </div>

                    @if($record->assigned_to)
                        <div class="st-timeline-item">
                            <div class="st-timeline-dot" style="background: #4f46e5;"></div>
                            <p class="st-timeline-text">Assigned to {{ $record->assignee?->name }}</p>
                            <p class="st-timeline-date">{{ $record->created_at->format('M d, h:i A') }}</p>
                        </div>
                    @endif

                    @if($record->last_reply_at)
                        <div class="st-timeline-item">
                            <div class="st-timeline-dot" style="background: #3b82f6;"></div>
                            <p class="st-timeline-text">Last reply</p>
                            <p class="st-timeline-date">{{ $record->last_reply_at->format('M d, h:i A') }}</p>
                        </div>
                    @endif

                    @if($record->resolved_at)
                        <div class="st-timeline-item">
                            <div class="st-timeline-dot" style="background: #22c55e;"></div>
                            <p class="st-timeline-text">Ticket resolved</p>
                            <p class="st-timeline-date">{{ $record->resolved_at->format('M d, h:i A') }}</p>
                        </div>
                    @endif

                    @if($record->closed_at)
                        <div class="st-timeline-item">
                            <div class="st-timeline-dot" style="background: #94a3b8;"></div>
                            <p class="st-timeline-text">Ticket closed</p>
                            <p class="st-timeline-date">{{ $record->closed_at->format('M d, h:i A') }}</p>
                        </div>
                    @endif

                    <div class="st-timeline-item">
                        <div class="st-timeline-dot" style="background: #e2e8f0;"></div>
                        <p class="st-timeline-text" style="color: #94a3b8;">{{ $record->messages->count() }} messages total</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
