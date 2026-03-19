<x-filament-panels::page>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ─── LEFT: Conversation Thread ─── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Ticket Header Card --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-5">
                <div class="flex items-start justify-between gap-4">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-xs font-mono text-gray-400">{{ $record->ticket_number }}</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background-color: {{ $record->status_color }}20; color: {{ $record->status_color }}">
                                {{ str_replace('_', ' ', ucwords($record->status, '_')) }}
                            </span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold"
                                  style="background-color: {{ $record->priority_color }}20; color: {{ $record->priority_color }}">
                                {{ ucfirst($record->priority) }}
                            </span>
                        </div>
                        <h2 class="text-lg font-bold text-gray-900 dark:text-white truncate">
                            {{ $record->subject }}
                        </h2>
                    </div>
                </div>
                @if($record->description)
                    <div class="mt-3 text-sm text-gray-600 dark:text-gray-400 bg-gray-50 dark:bg-gray-800/50 rounded-xl p-4">
                        {!! nl2br(e($record->description)) !!}
                    </div>
                @endif
            </div>

            {{-- Messages Thread --}}
            <div class="space-y-3" id="messages-container">
                @forelse($record->messages as $message)
                    <div class="flex {{ $message->isFromAgent() ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[85%] {{ $message->isSystemMessage() ? 'w-full' : '' }}">

                            {{-- System Message --}}
                            @if($message->isSystemMessage())
                                <div class="text-center py-2">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 text-xs text-gray-400 bg-gray-50 dark:bg-gray-800/50 rounded-full">
                                        <x-heroicon-o-information-circle class="w-3.5 h-3.5" />
                                        {{ $message->message }}
                                        <span class="text-gray-300">·</span>
                                        {{ $message->created_at->diffForHumans() }}
                                    </span>
                                </div>
                            @else
                                {{-- Chat Bubble --}}
                                <div class="rounded-2xl p-4 shadow-sm
                                    {{ $message->isFromAgent()
                                        ? 'bg-primary-600 text-white rounded-br-md'
                                        : 'bg-white dark:bg-gray-900 border border-gray-100 dark:border-gray-800 text-gray-800 dark:text-gray-200 rounded-bl-md' }}
                                    {{ $message->is_internal ? 'border-2 border-dashed border-yellow-400 !bg-yellow-50 dark:!bg-yellow-900/10 !text-yellow-800 dark:!text-yellow-200' : '' }}">

                                    @if($message->is_internal)
                                        <div class="flex items-center gap-1 text-xs text-yellow-600 mb-2 font-semibold">
                                            <x-heroicon-o-lock-closed class="w-3 h-3" />
                                            Internal Note
                                        </div>
                                    @endif

                                    <p class="text-sm leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>

                                    @if($message->attachment)
                                        <a href="{{ Storage::url($message->attachment) }}" target="_blank"
                                           class="inline-flex items-center gap-1 mt-2 text-xs underline {{ $message->isFromAgent() ? 'text-white/80' : 'text-primary-600' }}">
                                            <x-heroicon-o-paper-clip class="w-3.5 h-3.5" />
                                            Attachment
                                        </a>
                                    @endif

                                    <div class="flex items-center gap-2 mt-2 {{ $message->isFromAgent() ? 'text-white/60' : 'text-gray-400' }} text-xs">
                                        <span>{{ $message->sender?->name ?? 'System' }}</span>
                                        <span>·</span>
                                        <span>{{ $message->created_at->format('M d, h:i A') }}</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 mx-auto text-gray-300 mb-3" />
                        <p class="text-gray-400 text-sm">No messages yet. Send the first reply below.</p>
                    </div>
                @endforelse
            </div>

            {{-- Reply Box --}}
            @if($record->status !== 'closed')
                <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-4">
                    <form wire:submit.prevent="sendReply">
                        <textarea wire:model="replyMessage"
                            rows="3"
                            placeholder="Type your reply..."
                            class="w-full rounded-xl border-gray-200 dark:border-gray-700 dark:bg-gray-800 focus:border-primary-500 focus:ring-primary-500 text-sm resize-none"
                        ></textarea>

                        <div class="flex items-center justify-between mt-3">
                            <div class="flex items-center gap-3">
                                <label class="flex items-center gap-2 cursor-pointer text-sm text-gray-500 hover:text-gray-700 transition">
                                    <input type="file" wire:model="replyAttachment" class="hidden" />
                                    <x-heroicon-o-paper-clip class="w-5 h-5" />
                                    <span>{{ $replyAttachment ? 'File selected' : 'Attach' }}</span>
                                </label>

                                <label class="flex items-center gap-2 cursor-pointer text-sm">
                                    <input type="checkbox" wire:model="isInternal"
                                           class="rounded border-gray-300 text-yellow-500 focus:ring-yellow-500" />
                                    <span class="text-yellow-600 dark:text-yellow-400 font-medium">Internal Note</span>
                                </label>
                            </div>

                            <button type="submit"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 text-white text-sm font-semibold rounded-xl hover:bg-primary-700 transition shadow-sm"
                                wire:loading.attr="disabled">
                                <x-heroicon-o-paper-airplane class="w-4 h-4" />
                                <span wire:loading.remove>Send Reply</span>
                                <span wire:loading>Sending...</span>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <div class="bg-gray-50 dark:bg-gray-800/30 rounded-2xl p-6 text-center">
                    <x-heroicon-o-lock-closed class="w-8 h-8 mx-auto text-gray-300 mb-2" />
                    <p class="text-gray-400 text-sm">This ticket is closed. Reopen it to send replies.</p>
                </div>
            @endif
        </div>

        {{-- ─── RIGHT: Ticket Details Sidebar ─── --}}
        <div class="space-y-4">

            {{-- Quick Status Change --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Status</h3>
                <div class="grid grid-cols-2 gap-2">
                    @foreach(['open' => '🔵 Open', 'in_progress' => '🟡 In Progress', 'resolved' => '🟢 Resolved', 'closed' => '⚪ Closed'] as $val => $label)
                        <button wire:click="updateStatus('{{ $val }}')"
                            class="px-3 py-2 text-xs font-medium rounded-lg border transition
                                {{ $record->status === $val
                                    ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 ring-1 ring-primary-200'
                                    : 'border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-600 dark:text-gray-400' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Priority --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-4">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Priority</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach(['low' => ['label' => 'Low', 'color' => '#94a3b8'], 'medium' => ['label' => 'Medium', 'color' => '#3b82f6'], 'high' => ['label' => 'High', 'color' => '#f97316'], 'urgent' => ['label' => 'Urgent', 'color' => '#ef4444']] as $val => $meta)
                        <button wire:click="updatePriority('{{ $val }}')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-full transition
                                {{ $record->priority === $val
                                    ? 'text-white shadow-sm'
                                    : 'border border-gray-200 dark:border-gray-700 hover:opacity-80 text-gray-600 dark:text-gray-400' }}"
                            style="{{ $record->priority === $val ? 'background-color: ' . $meta['color'] : '' }}">
                            {{ $meta['label'] }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Details --}}
            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 p-4 space-y-3">
                <h3 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-1">Details</h3>

                <div>
                    <p class="text-xs text-gray-400">Client</p>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $record->client?->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-gray-400">Assigned Agent</p>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $record->assignee?->name ?? 'Unassigned' }}</p>
                </div>

                @if($record->category)
                <div>
                    <p class="text-xs text-gray-400">Category</p>
                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ ucfirst($record->category) }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs text-gray-400">Created</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->created_at->format('M d, Y h:i A') }}</p>
                </div>

                @if($record->last_reply_at)
                <div>
                    <p class="text-xs text-gray-400">Last Reply</p>
                    <p class="text-sm text-gray-600 dark:text-gray-400">{{ $record->last_reply_at->diffForHumans() }}</p>
                </div>
                @endif

                @if($record->resolved_at)
                <div>
                    <p class="text-xs text-gray-400">Resolved</p>
                    <p class="text-sm text-green-600">{{ $record->resolved_at->format('M d, Y h:i A') }}</p>
                </div>
                @endif

                <div>
                    <p class="text-xs text-gray-400">Messages</p>
                    <p class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $record->messages->count() }}</p>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
