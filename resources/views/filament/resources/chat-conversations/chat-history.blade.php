<div class="space-y-3 max-h-[500px] overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900 rounded-xl">
    @forelse($getRecord()->messages()->orderBy('created_at')->get() as $msg)
        <div class="flex {{ $msg->role === 'user' ? 'justify-end' : 'justify-start' }}">
            <div class="max-w-[80%] px-4 py-2.5 rounded-2xl text-sm
                {{ $msg->role === 'user'
                    ? 'bg-blue-600 text-white rounded-tr-md'
                    : ($msg->role === 'agent'
                        ? 'bg-green-100 text-green-900 dark:bg-green-900 dark:text-green-100 rounded-tl-md border border-green-200 dark:border-green-800'
                        : 'bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200 rounded-tl-md border border-gray-200 dark:border-gray-700') }}">

                @if($msg->role !== 'user')
                    <span class="text-[10px] font-semibold uppercase tracking-wide
                        {{ $msg->role === 'agent' ? 'text-green-600 dark:text-green-400' : 'text-blue-600 dark:text-blue-400' }}">
                        {{ $msg->role === 'agent' ? '👤 Agent' : '🤖 Bot' }}
                    </span>
                @endif

                <p class="whitespace-pre-line leading-relaxed">{{ $msg->message }}</p>
                <p class="text-[10px] mt-1 {{ $msg->role === 'user' ? 'text-blue-200' : 'text-gray-400' }}">
                    {{ $msg->created_at->format('h:i A, d M') }}
                </p>
            </div>
        </div>
    @empty
        <div class="text-center text-gray-400 py-8">
            <svg class="w-10 h-10 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <p>No messages yet</p>
        </div>
    @endforelse
</div>
