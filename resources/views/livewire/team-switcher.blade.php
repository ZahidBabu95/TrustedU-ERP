<div class="team-switcher">
    <div x-data="{ open: false }" class="relative">
        <button @click="open = !open" type="button"
            class="flex items-center gap-2 px-3 py-2 text-sm font-medium rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors"
        >
            @if($currentTeam)
                <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-xs font-bold text-white"
                      style="background-color: {{ $currentTeam->color ?? '#6366f1' }}">
                    {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                </span>
                <span class="max-w-[120px] truncate">{{ $currentTeam->name }}</span>
            @else
                <x-heroicon-o-building-office class="w-5 h-5 text-gray-400" />
                <span class="text-gray-500">{{ $isAdmin ? 'All Teams' : 'Select Team' }}</span>
            @endif
            <x-heroicon-o-chevron-down class="w-4 h-4 text-gray-400" />
        </button>

        <div x-show="open" @click.away="open = false"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="absolute right-0 z-50 mt-2 w-56 origin-top-right rounded-xl bg-white dark:bg-gray-900 shadow-lg ring-1 ring-black ring-opacity-5 p-1"
             style="display: none;"
        >
            <div class="px-3 py-2 text-xs font-semibold text-gray-400 uppercase tracking-wider">Switch Team</div>

            @if($isAdmin)
                <button wire:click="clearTeam" @click="open = false"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors
                        {{ !$selectedTeamId ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300' }}"
                >
                    <x-heroicon-o-globe-alt class="w-5 h-5" />
                    <span>All Teams</span>
                    @if(!$selectedTeamId)
                        <x-heroicon-s-check class="w-4 h-4 ml-auto text-primary-600" />
                    @endif
                </button>
            @endif

            @foreach($teams as $team)
                <button wire:click="switchTeam({{ $team->id }})" @click="open = false"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm rounded-lg transition-colors
                        {{ $selectedTeamId == $team->id ? 'bg-primary-50 text-primary-700 dark:bg-primary-900/20 dark:text-primary-400' : 'hover:bg-gray-50 dark:hover:bg-gray-800 text-gray-700 dark:text-gray-300' }}"
                >
                    <span class="inline-flex items-center justify-center w-6 h-6 rounded-md text-xs font-bold text-white flex-shrink-0"
                          style="background-color: {{ $team->color ?? '#6366f1' }}">
                        {{ strtoupper(substr($team->name, 0, 1)) }}
                    </span>
                    <span class="truncate">{{ $team->name }}</span>
                    @if($selectedTeamId == $team->id)
                        <x-heroicon-s-check class="w-4 h-4 ml-auto text-primary-600 flex-shrink-0" />
                    @endif
                </button>
            @endforeach

            @if($teams->isEmpty())
                <div class="px-3 py-4 text-center text-sm text-gray-400">
                    No teams available
                </div>
            @endif
        </div>
    </div>
</div>
