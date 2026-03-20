ূ<x-filament-panels::page>
    @php
        $dealsByStage = $this->getDealsByStage();
        $stats = $this->getKanbanStats();
        $stageLabels = \App\Models\Deal::STAGE_LABELS;
        $stageColors = \App\Models\Deal::STAGE_COLORS;
        $sourceLabels = \App\Models\Deal::SOURCE_LABELS;
    @endphp

    <style>
        /* ━━━ Deal Kanban Styles ━━━ */
        .dk-container { display: flex; gap: 1.25rem; overflow-x: auto; padding-bottom: 1rem; min-height: 70vh; }
        .dk-container::-webkit-scrollbar { height: 6px; }
        .dk-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .dk-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .dk-column { min-width: 290px; width: 290px; flex-shrink: 0; display: flex; flex-direction: column; gap: 0.75rem; }
        .dk-column-header { display: flex; align-items: center; justify-content: space-between; padding: 0 0.25rem; margin-bottom: 0.25rem; }
        .dk-column-title { display: flex; align-items: center; gap: 0.5rem; }
        .dk-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .dk-count { background: #f1f5f9; color: #64748b; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 9999px; }
        .dk-value-sum { font-size: 10px; font-weight: 700; color: #94a3b8; }

        .dk-card {
            background: white; border-radius: 0.75rem; border: 1px solid rgba(0,0,0,0.06);
            padding: 1rem; transition: all 0.2s; cursor: grab; position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dk-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); }
        .dk-card:active { cursor: grabbing; }
        .dk-card.dragging { opacity: 0.5; transform: rotate(2deg); }
        .dark .dk-card { background: rgb(17 24 39); border-color: rgb(31 41 55); }

        .dk-card-title { font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 2px; text-decoration: none; display: block; }
        .dk-card-title:hover { color: #4f46e5; }
        .dark .dk-card-title { color: #f1f5f9; }
        .dk-card-company { font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.5rem; }

        .dk-badge { display: inline-flex; align-items: center; padding: 2px 8px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 9999px; }
        .dk-source-badge { background: #eef2ff; color: #4f46e5; border: 1px solid #e0e7ff; }
        .dk-priority-high { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
        .dk-priority-urgent { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
        .dk-priority-medium { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .dk-priority-low { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

        .dk-value-row { display: flex; align-items: center; justify-content: space-between; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #f8fafc; }
        .dark .dk-value-row { border-color: #1f2937; }
        .dk-value-label { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #94a3b8; }
        .dk-value-amount { font-size: 0.875rem; font-weight: 800; color: #4f46e5; }

        .dk-avatar { width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #8b5cf6, #6366f1); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: white; flex-shrink: 0; }

        .dk-drop-zone { min-height: 80px; border: 2px dashed transparent; border-radius: 0.75rem; transition: all 0.2s; flex: 1; }
        .dk-drop-zone.drag-over { border-color: #8b5cf6; background: rgba(139,92,246,0.04); }

        .dk-add-btn {
            width: 100%; border: 2px dashed #e2e8f0; border-radius: 0.75rem; padding: 0.75rem;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            color: #94a3b8; font-size: 0.8rem; font-weight: 600; cursor: pointer;
            transition: all 0.2s; background: transparent; text-decoration: none;
        }
        .dk-add-btn:hover { border-color: #8b5cf6; color: #8b5cf6; background: rgba(139,92,246,0.02); }

        .dk-stats-bar { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .dk-stat-card {
            background: white; padding: 0.75rem 1.25rem; border-radius: 0.75rem;
            border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dark .dk-stat-card { background: rgb(17 24 39); border-color: rgb(31 41 55); }
        .dk-stat-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; }
        .dk-stat-value { font-size: 1.125rem; font-weight: 800; color: #0f172a; }
        .dark .dk-stat-value { color: #f1f5f9; }

        .dk-progress-track { width: 100%; height: 4px; background: #f1f5f9; border-radius: 9999px; overflow: hidden; margin: 0.4rem 0; }
        .dark .dk-progress-track { background: #1f2937; }
        .dk-progress-fill { height: 100%; border-radius: 9999px; transition: width 0.3s; }

        .dk-close-btn {
            width: 100%; margin-top: 0.75rem; padding: 0.5rem; border-radius: 0.5rem;
            font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
            border: none; cursor: pointer; transition: all 0.2s;
        }
        .dk-close-won { background: #ecfdf5; color: #059669; }
        .dk-close-won:hover { background: #10b981; color: white; }
        .dk-close-lost { background: #fef2f2; color: #ef4444; }
        .dk-close-lost:hover { background: #ef4444; color: white; }

        .dk-client-link { display: flex; align-items: center; gap: 0.35rem; font-size: 0.7rem; color: #6366f1; font-weight: 600; margin-top: 0.25rem; text-decoration: none; }
        .dk-client-link:hover { text-decoration: underline; }
    </style>

    <div style="display: flex; flex-direction: column; gap: 1rem;">

        {{-- ═══ STATS BAR ═══ --}}
        <div class="dk-stats-bar">
            <div class="dk-stat-card">
                <p class="dk-stat-label">Pipeline Value</p>
                <p class="dk-stat-value">৳{{ number_format($stats['total_value']) }}</p>
            </div>
            <div class="dk-stat-card">
                <p class="dk-stat-label">Active Deals</p>
                <p class="dk-stat-value">{{ $stats['active_deals'] }}</p>
            </div>
            <div class="dk-stat-card">
                <p class="dk-stat-label">Weighted Value</p>
                <p class="dk-stat-value" style="color: #8b5cf6;">৳{{ number_format($stats['weighted_value']) }}</p>
            </div>
            <div class="dk-stat-card">
                <p class="dk-stat-label">Won</p>
                <p class="dk-stat-value" style="color: #10b981;">{{ $stats['won_count'] }} <span style="font-size: 0.75rem; font-weight: 600;">(৳{{ number_format($stats['won_value']) }})</span></p>
            </div>
            <div class="dk-stat-card">
                <p class="dk-stat-label">Lost</p>
                <p class="dk-stat-value" style="color: #ef4444;">{{ $stats['lost_count'] }}</p>
            </div>
        </div>

        {{-- ═══ KANBAN PIPELINE ═══ --}}
        <div class="dk-container" id="deal-kanban">
            @foreach(\App\Models\Deal::KANBAN_STAGES as $stage)
                @php
                    $deals = $dealsByStage[$stage];
                    $color = $stageColors[$stage];
                    $stageTotal = $deals->sum('value');
                @endphp
                <div class="dk-column" data-stage="{{ $stage }}">
                    {{-- Column Header --}}
                    <div class="dk-column-header">
                        <div class="dk-column-title">
                            <span class="dk-dot" style="background: {{ $color }};"></span>
                            <h3 style="font-size: 0.875rem; font-weight: 800; color: #0f172a;" class="dark:text-white">{{ $stageLabels[$stage] }}</h3>
                            <span class="dk-count">{{ $deals->count() }}</span>
                        </div>
                        <span class="dk-value-sum">৳{{ number_format($stageTotal) }}</span>
                    </div>

                    {{-- Drop Zone --}}
                    <div class="dk-drop-zone"
                         data-stage="{{ $stage }}"
                         ondragover="event.preventDefault(); this.classList.add('drag-over');"
                         ondragleave="this.classList.remove('drag-over');"
                         ondrop="handleDealDrop(event, '{{ $stage }}')">

                        @foreach($deals as $deal)
                            <div class="dk-card" style="margin-bottom: 0.75rem;"
                                 draggable="true"
                                 data-deal-id="{{ $deal->id }}"
                                 ondragstart="handleDealDragStart(event, {{ $deal->id }})"
                                 ondragend="handleDealDragEnd(event)">

                                {{-- Badges --}}
                                <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
                                    @if($deal->deal_source)
                                        <span class="dk-badge dk-source-badge">{{ $sourceLabels[$deal->deal_source] ?? $deal->deal_source }}</span>
                                    @endif
                                    @if($deal->priority && $deal->priority !== 'medium')
                                        <span class="dk-badge dk-priority-{{ $deal->priority }}">
                                            {{ \App\Models\Deal::PRIORITY_LABELS[$deal->priority] ?? $deal->priority }}
                                        </span>
                                    @endif
                                    @if($deal->label)
                                        <span class="dk-badge" style="background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;">{{ $deal->label }}</span>
                                    @endif
                                </div>

                                {{-- Title & Company --}}
                                <a href="{{ \App\Filament\Resources\Deals\DealResource::getUrl('edit', ['record' => $deal]) }}"
                                   class="dk-card-title">{{ $deal->title }}</a>
                                @if($deal->company)
                                    <p class="dk-card-company">{{ $deal->company }}</p>
                                @endif

                                {{-- Client link --}}
                                @if($deal->client)
                                    <a href="{{ \App\Filament\Resources\Clients\ClientResource::getUrl('view', ['record' => $deal->client]) }}"
                                       class="dk-client-link">
                                        <x-heroicon-o-building-office-2 style="width: 12px; height: 12px;" />
                                        {{ $deal->client->name }}
                                    </a>
                                @endif

                                {{-- Probability progress --}}
                                @if($deal->probability > 0)
                                    <div class="dk-progress-track">
                                        <div class="dk-progress-fill" style="width: {{ $deal->probability }}%; background: {{ $color }};"></div>
                                    </div>
                                    <span style="font-size: 9px; color: #94a3b8; font-weight: 600;">{{ $deal->probability }}% probability</span>
                                @endif

                                {{-- Value & Avatar --}}
                                <div class="dk-value-row">
                                    <div>
                                        <span class="dk-value-label">Value</span><br>
                                        <span class="dk-value-amount">৳{{ $deal->value ? number_format($deal->value) : '—' }}</span>
                                    </div>
                                    @if($deal->assignee)
                                        <div class="dk-avatar" title="{{ $deal->assignee->name }}">
                                            {{ strtoupper(substr($deal->assignee->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Close Buttons for Contract stage --}}
                                @if($stage === 'contract')
                                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                                        <button class="dk-close-btn dk-close-won" style="flex: 1;"
                                                wire:click="updateDealStage({{ $deal->id }}, 'closed_won')">
                                            <x-heroicon-o-check-circle style="width: 14px; height: 14px;" />
                                            Won
                                        </button>
                                        <button class="dk-close-btn dk-close-lost" style="flex: 1;"
                                                wire:click="updateDealStage({{ $deal->id }}, 'closed_lost')">
                                            <x-heroicon-o-x-circle style="width: 14px; height: 14px;" />
                                            Lost
                                        </button>
                                    </div>
                                @endif
                            </div>
                        @endforeach

                        @if($deals->isEmpty())
                            <div style="padding: 2rem 1rem; text-align: center;">
                                <x-heroicon-o-inbox style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #e2e8f0;" />
                                <p style="font-size: 0.8rem; color: #94a3b8;">No deals here</p>
                            </div>
                        @endif
                    </div>

                    {{-- Add Deal Button --}}
                    <a href="{{ \App\Filament\Resources\Deals\DealResource::getUrl('create') }}?stage={{ $stage }}" class="dk-add-btn">
                        <x-heroicon-o-plus style="width: 16px; height: 16px;" />
                        Add Deal
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ Drag & Drop JavaScript ═══ --}}
    <script>
        let draggedDealId = null;

        function handleDealDragStart(event, dealId) {
            draggedDealId = dealId;
            event.target.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', dealId);
        }

        function handleDealDragEnd(event) {
            event.target.classList.remove('dragging');
            document.querySelectorAll('.dk-drop-zone').forEach(z => z.classList.remove('drag-over'));
        }

        function handleDealDrop(event, newStage) {
            event.preventDefault();
            event.currentTarget.classList.remove('drag-over');

            if (draggedDealId) {
                @this.call('updateDealStage', draggedDealId, newStage);
                draggedDealId = null;
            }
        }
    </script>
</x-filament-panels::page>
