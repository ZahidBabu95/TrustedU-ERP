<x-filament-panels::page>
    @php
        $leadsByStatus = $this->getLeadsByStatus();
        $stats = $this->getKanbanStats();
        $statusLabels = \App\Models\Lead::STATUS_LABELS;
        $statusColors = \App\Models\Lead::STATUS_COLORS;
        $sourceLabels = \App\Models\Lead::SOURCE_LABELS;
    @endphp

    <style>
        /* ━━━ Kanban Custom Styles ━━━ */
        .kb-container { display: flex; gap: 1.25rem; overflow-x: auto; padding-bottom: 1rem; min-height: 70vh; }
        .kb-container::-webkit-scrollbar { height: 6px; }
        .kb-container::-webkit-scrollbar-track { background: #f1f5f9; border-radius: 10px; }
        .kb-container::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }

        .kb-column { min-width: 300px; width: 300px; flex-shrink: 0; display: flex; flex-direction: column; gap: 0.75rem; }
        .kb-column-header { display: flex; align-items: center; justify-content: space-between; padding: 0 0.25rem; margin-bottom: 0.25rem; }
        .kb-column-title { display: flex; align-items: center; gap: 0.5rem; }
        .kb-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
        .kb-count { background: #f1f5f9; color: #64748b; font-size: 10px; font-weight: 700; padding: 2px 6px; border-radius: 9999px; }

        .kb-card {
            background: white; border-radius: 0.75rem; border: 1px solid rgba(0,0,0,0.06);
            padding: 1rem; transition: all 0.2s; cursor: grab; position: relative;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .kb-card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.08); transform: translateY(-1px); }
        .kb-card:active { cursor: grabbing; }
        .kb-card.dragging { opacity: 0.5; transform: rotate(2deg); }
        .dark .kb-card { background: rgb(17 24 39); border-color: rgb(31 41 55); }

        .kb-card-name { font-size: 0.9rem; font-weight: 700; color: #0f172a; margin-bottom: 2px; }
        .dark .kb-card-name { color: #f1f5f9; }
        .kb-card-company { font-size: 0.75rem; color: #94a3b8; margin-bottom: 0.75rem; }

        .kb-badge { display: inline-flex; align-items: center; padding: 2px 8px; font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; border-radius: 9999px; }
        .kb-priority-high { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
        .kb-priority-urgent { background: #fef2f2; color: #dc2626; border: 1px solid #fca5a5; }
        .kb-priority-medium { background: #fffbeb; color: #d97706; border: 1px solid #fde68a; }
        .kb-priority-low { background: #f0fdf4; color: #16a34a; border: 1px solid #bbf7d0; }

        .kb-source-badge { background: #eef2ff; color: #4f46e5; border: 1px solid #e0e7ff; }

        .kb-value-row { display: flex; align-items: center; justify-content: space-between; margin-top: 0.75rem; padding-top: 0.5rem; border-top: 1px solid #f8fafc; }
        .dark .kb-value-row { border-color: #1f2937; }
        .kb-value-label { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #94a3b8; }
        .kb-value-amount { font-size: 0.875rem; font-weight: 800; color: #4f46e5; }

        .kb-avatar { width: 24px; height: 24px; border-radius: 50%; background: linear-gradient(135deg, #6366f1, #4f46e5); display: flex; align-items: center; justify-content: center; font-size: 9px; font-weight: 700; color: white; flex-shrink: 0; }

        .kb-drop-zone { min-height: 80px; border: 2px dashed transparent; border-radius: 0.75rem; transition: all 0.2s; flex: 1; }
        .kb-drop-zone.drag-over { border-color: #6366f1; background: rgba(99,102,241,0.04); }

        .kb-add-btn {
            width: 100%; border: 2px dashed #e2e8f0; border-radius: 0.75rem; padding: 0.75rem;
            display: flex; align-items: center; justify-content: center; gap: 0.5rem;
            color: #94a3b8; font-size: 0.8rem; font-weight: 600; cursor: pointer;
            transition: all 0.2s; background: transparent;
        }
        .kb-add-btn:hover { border-color: #6366f1; color: #6366f1; background: rgba(99,102,241,0.02); }

        .kb-stats-bar { display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem; flex-wrap: wrap; }
        .kb-stat-card {
            background: white; padding: 0.75rem 1.25rem; border-radius: 0.75rem;
            border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dark .kb-stat-card { background: rgb(17 24 39); border-color: rgb(31 41 55); }
        .kb-stat-label { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; }
        .kb-stat-value { font-size: 1.125rem; font-weight: 800; color: #0f172a; }
        .dark .kb-stat-value { color: #f1f5f9; }

        .kb-convert-btn {
            width: 100%; margin-top: 0.75rem; padding: 0.5rem; border-radius: 0.5rem;
            font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em;
            display: flex; align-items: center; justify-content: center; gap: 0.4rem;
            background: #ecfdf5; color: #059669; border: none; cursor: pointer;
            transition: all 0.2s;
        }
        .kb-convert-btn:hover { background: #10b981; color: white; }

        .kb-progress-bar { display: flex; gap: 3px; margin: 0.5rem 0; }
        .kb-progress-segment { height: 3px; flex: 1; border-radius: 9999px; }
    </style>

    <div style="display: flex; flex-direction: column; gap: 1rem;">

        {{-- ═══ STATS BAR ═══ --}}
        <div class="kb-stats-bar">
            <div class="kb-stat-card">
                <p class="kb-stat-label">Total Value</p>
                <p class="kb-stat-value">৳{{ number_format($stats['total_value']) }}</p>
            </div>
            <div class="kb-stat-card">
                <p class="kb-stat-label">Active Leads</p>
                <p class="kb-stat-value">{{ $stats['active_leads'] }}</p>
            </div>
            <div class="kb-stat-card">
                <p class="kb-stat-label">Won</p>
                <p class="kb-stat-value" style="color: #10b981;">{{ $stats['won_count'] }}</p>
            </div>
            <div class="kb-stat-card">
                <p class="kb-stat-label">Lost</p>
                <p class="kb-stat-value" style="color: #ef4444;">{{ $stats['lost_count'] }}</p>
            </div>
            <div style="margin-left: auto;">
                {{-- view toggle could go here --}}
            </div>
        </div>

        {{-- ═══ KANBAN BOARD ═══ --}}
        <div class="kb-container" id="kanban-board">
            @foreach(\App\Models\Lead::KANBAN_STATUSES as $status)
                @php
                    $leads = $leadsByStatus[$status];
                    $color = $statusColors[$status];
                @endphp
                <div class="kb-column" data-status="{{ $status }}">
                    {{-- Column Header --}}
                    <div class="kb-column-header">
                        <div class="kb-column-title">
                            <span class="kb-dot" style="background: {{ $color }};"></span>
                            <h3 style="font-size: 0.875rem; font-weight: 800; color: #0f172a;" class="dark:text-white">{{ $statusLabels[$status] }}</h3>
                            <span class="kb-count">{{ $leads->count() }}</span>
                        </div>
                    </div>

                    {{-- Drop Zone --}}
                    <div class="kb-drop-zone"
                         data-status="{{ $status }}"
                         ondragover="event.preventDefault(); this.classList.add('drag-over');"
                         ondragleave="this.classList.remove('drag-over');"
                         ondrop="handleDrop(event, '{{ $status }}')">

                        @foreach($leads as $lead)
                            <div class="kb-card" style="margin-bottom: 0.75rem;"
                                 draggable="true"
                                 data-lead-id="{{ $lead->id }}"
                                 ondragstart="handleDragStart(event, {{ $lead->id }})"
                                 ondragend="handleDragEnd(event)">

                                {{-- Badges Row --}}
                                <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.5rem; flex-wrap: wrap;">
                                    @if($lead->source)
                                        <span class="kb-badge kb-source-badge">{{ $sourceLabels[$lead->source] ?? $lead->source }}</span>
                                    @endif
                                    @if($lead->priority && $lead->priority !== 'medium')
                                        <span class="kb-badge kb-priority-{{ $lead->priority }}">
                                            {{ \App\Models\Lead::PRIORITY_LABELS[$lead->priority] ?? $lead->priority }}
                                        </span>
                                    @endif
                                    @if($lead->label)
                                        <span class="kb-badge" style="background: #fff7ed; color: #ea580c; border: 1px solid #fed7aa;">{{ $lead->label }}</span>
                                    @endif
                                </div>

                                {{-- Name & Company --}}
                                <a href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('edit', ['record' => $lead]) }}"
                                   style="text-decoration: none;">
                                    <p class="kb-card-name">{{ $lead->name }}</p>
                                </a>
                                @if($lead->company)
                                    <p class="kb-card-company">{{ $lead->company }}</p>
                                @endif

                                {{-- Progress for negotiation --}}
                                @if($status === 'negotiation')
                                    <div class="kb-progress-bar">
                                        <div class="kb-progress-segment" style="background: #6366f1;"></div>
                                        <div class="kb-progress-segment" style="background: #6366f1;"></div>
                                        <div class="kb-progress-segment" style="background: #6366f1;"></div>
                                        <div class="kb-progress-segment" style="background: #e2e8f0;"></div>
                                    </div>
                                @endif

                                {{-- Value & Avatar --}}
                                <div class="kb-value-row">
                                    <div>
                                        <span class="kb-value-label">Value</span><br>
                                        <span class="kb-value-amount">৳{{ $lead->value ? number_format($lead->value) : '—' }}</span>
                                    </div>
                                    @if($lead->assignee)
                                        <div class="kb-avatar" title="{{ $lead->assignee->name }}">
                                            {{ strtoupper(substr($lead->assignee->name, 0, 2)) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Convert Button for Negotiation --}}
                                @if($status === 'negotiation')
                                    <button class="kb-convert-btn" wire:click="updateLeadStatus({{ $lead->id }}, 'won')">
                                        <x-heroicon-o-check-badge style="width: 14px; height: 14px;" />
                                        Convert to Won
                                    </button>
                                @endif
                            </div>
                        @endforeach

                        @if($leads->isEmpty())
                            <div style="padding: 2rem 1rem; text-align: center;">
                                <x-heroicon-o-inbox style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #e2e8f0;" />
                                <p style="font-size: 0.8rem; color: #94a3b8;">No leads here</p>
                            </div>
                        @endif
                    </div>

                    {{-- Add Lead Button --}}
                    <a href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('create') }}?status={{ $status }}" class="kb-add-btn" style="text-decoration: none;">
                        <x-heroicon-o-plus style="width: 16px; height: 16px;" />
                        Add Lead
                    </a>
                </div>
            @endforeach
        </div>
    </div>

    {{-- ═══ Drag & Drop JavaScript ═══ --}}
    <script>
        let draggedLeadId = null;

        function handleDragStart(event, leadId) {
            draggedLeadId = leadId;
            event.target.classList.add('dragging');
            event.dataTransfer.effectAllowed = 'move';
            event.dataTransfer.setData('text/plain', leadId);
        }

        function handleDragEnd(event) {
            event.target.classList.remove('dragging');
            document.querySelectorAll('.kb-drop-zone').forEach(z => z.classList.remove('drag-over'));
        }

        function handleDrop(event, newStatus) {
            event.preventDefault();
            event.currentTarget.classList.remove('drag-over');

            if (draggedLeadId) {
                @this.call('updateLeadStatus', draggedLeadId, newStatus);
                draggedLeadId = null;
            }
        }
    </script>
</x-filament-panels::page>
