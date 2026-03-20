@php
    $initials = fn(string $name): string => collect(explode(' ', $name))
        ->map(fn($w) => strtoupper($w[0] ?? ''))
        ->take(2)->implode('');
@endphp

<div class="erp-dash" x-data="{
    searchQuery: '',
    searchResults: [],
    searchLoading: false,
    searchOpen: false,
    async doSearch() {
        if (this.searchQuery.length < 2) { this.searchResults = []; return; }
        this.searchLoading = true;
        try {
            const r = await fetch('/admin/api/dashboard-search?q=' + encodeURIComponent(this.searchQuery));
            this.searchResults = await r.json();
        } catch(e) { this.searchResults = []; }
        this.searchLoading = false;
    }
}">

{{-- ══ HEADER ══ --}}
<div class="dash-header">
    <div class="dash-header-left">
        <div class="dash-greeting">
            <span class="dash-wave">👋</span>
            <div>
                <h1 class="dash-title">Welcome back, {{ auth()->user()->name ?? 'Admin' }}</h1>
                <p class="dash-subtitle">
                    {{ now()->format('l, F d, Y') }} —
                    @if($currentTeam)
                        Viewing <strong style="color:#6c5ce7;">{{ $currentTeam->name }}</strong> data
                    @else
                        Here's your complete business overview
                    @endif
                </p>
            </div>
        </div>
    </div>
    <div class="dash-search-wrap" x-on:click.away="searchOpen = false">
        <div class="dash-search-box" :class="{ 'active': searchOpen }">
            <svg class="dash-search-icon" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" placeholder="Search Client ID, Ticket #, Lead..."
                   x-model="searchQuery" x-on:input.debounce.400ms="doSearch()" x-on:focus="searchOpen = true"
                   class="dash-search-input">
            <template x-if="searchLoading">
                <div class="dash-search-spinner"></div>
            </template>
            <kbd class="dash-search-kbd">⌘K</kbd>
        </div>
        <div x-show="searchOpen && searchQuery.length >= 2" x-transition.opacity class="dash-search-dropdown">
            <template x-if="searchResults.length === 0 && !searchLoading">
                <div class="dash-search-empty">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="#cbd5e1" stroke-width="1.5"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                    <p>No results found</p>
                </div>
            </template>
            <template x-for="(group, gi) in searchResults" :key="gi">
                <div>
                    <div class="dash-search-group" x-text="group.type"></div>
                    <template x-for="(item, ii) in group.items" :key="ii">
                        <a :href="item.url" class="dash-search-item">
                            <div class="dash-search-item-icon" :style="'background:' + (item.iconBg || '#eef2ff')">
                                <span x-text="item.icon || '🔍'"></span>
                            </div>
                            <div class="dash-search-item-info">
                                <span class="dash-search-item-title" x-text="item.title"></span>
                                <span class="dash-search-item-sub" x-text="item.subtitle"></span>
                            </div>
                            <span class="dash-search-item-badge" x-show="item.badge"
                                  :style="'background:' + (item.badgeBg || '#ecfdf5') + ';color:' + (item.badgeColor || '#059669')"
                                  x-text="item.badge"></span>
                        </a>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

{{-- ══ ALERT BANNERS ══ --}}
@if($urgentTickets > 0 || $overdueTasks > 0)
<div class="dash-alerts">
    @if($urgentTickets > 0)
    <a href="{{ route('filament.admin.resources.support-tickets.index') }}" class="dash-alert dash-alert-red">
        <div class="dash-alert-pulse"></div>
        <div class="dash-alert-icon red">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
        </div>
        <div><strong>{{ $urgentTickets }} Urgent Ticket{{ $urgentTickets > 1 ? 's' : '' }}</strong><br><span>Requires immediate attention</span></div>
    </a>
    @endif
    @if($overdueTasks > 0)
    <a href="{{ route('filament.admin.resources.tasks.index') }}" class="dash-alert dash-alert-amber">
        <div class="dash-alert-icon amber">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div><strong>{{ $overdueTasks }} Overdue Task{{ $overdueTasks > 1 ? 's' : '' }}</strong><br><span>Past due date</span></div>
    </a>
    @endif
</div>
@endif

{{-- ══ KPI CARDS ══ --}}
<div class="kpi-row">
    <div class="kpi-card kpi-leads">
        <div class="kpi-icon-wrap" style="--kpi-c1:#6c5ce7;--kpi-c2:#a78bfa;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Total Leads</span>
            <span class="kpi-value" x-data="{v:0}" x-init="setTimeout(()=>{let t=setInterval(()=>{v+={{ max(1,intval($totalLeads/20)) }};if(v>={{ $totalLeads }}){v={{ $totalLeads }};clearInterval(t)}},30)},200)" x-text="v">0</span>
            <div class="kpi-tags">
                @if($newLeadsToday > 0)<span class="kpi-tag green">+{{ $newLeadsToday }} today</span>@endif
                <span class="kpi-tag purple">{{ $newLeadsWeek }}/wk</span>
            </div>
        </div>
        <div class="kpi-spark">
            <svg viewBox="0 0 80 24" preserveAspectRatio="none"><defs><linearGradient id="sg1" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#6c5ce7" stop-opacity="0.2"/><stop offset="100%" stop-color="#6c5ce7" stop-opacity="0"/></linearGradient></defs><path d="M0,18 10,14 20,16 30,10 40,12 50,8 60,10 70,5 80,7 80,24 0,24Z" fill="url(#sg1)"/><polyline points="0,18 10,14 20,16 30,10 40,12 50,8 60,10 70,5 80,7" fill="none" stroke="#6c5ce7" stroke-width="1.5"/></svg>
        </div>
    </div>

    <div class="kpi-card kpi-clients">
        <div class="kpi-icon-wrap" style="--kpi-c1:#059669;--kpi-c2:#34d399;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-4h6v4"/></svg>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Active Clients</span>
            <span class="kpi-value">{{ $totalClients }}</span>
            <div class="kpi-tags"><span class="kpi-tag green">{{ $liveClients }} live</span></div>
        </div>
    </div>

    <div class="kpi-card kpi-pipeline">
        <div class="kpi-icon-wrap" style="--kpi-c1:#4f46e5;--kpi-c2:#818cf8;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Deal Pipeline</span>
            <span class="kpi-value">৳{{ number_format($pipelineValue) }}</span>
            <div class="kpi-tags"><span class="kpi-tag indigo">{{ $openDeals }} active</span></div>
        </div>
    </div>

    <div class="kpi-card kpi-won">
        <div class="kpi-icon-wrap" style="--kpi-c1:#ea580c;--kpi-c2:#fb923c;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Revenue Won</span>
            <span class="kpi-value">৳{{ number_format($wonDealValue) }}</span>
            <div class="kpi-tags"><span class="kpi-tag orange">{{ $closedWonCount }} won</span></div>
        </div>
    </div>

    <div class="kpi-card kpi-tickets">
        <div class="kpi-icon-wrap" style="--kpi-c1:#dc2626;--kpi-c2:#f87171;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Open Tickets</span>
            <span class="kpi-value">{{ $openTickets + $inProgressTickets }}</span>
            <div class="kpi-tags">
                <span class="kpi-tag blue">{{ $openTickets }} open</span>
                @if($highTickets > 0)<span class="kpi-tag orange">{{ $highTickets }} high</span>@endif
            </div>
        </div>
    </div>

    <div class="kpi-card kpi-conv">
        <div class="kpi-icon-wrap" style="--kpi-c1:#9333ea;--kpi-c2:#c084fc;">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
        </div>
        <div class="kpi-body">
            <span class="kpi-label">Conversion</span>
            <span class="kpi-value">{{ $convRate }}%</span>
            <div class="kpi-progress-bar"><div class="kpi-progress-fill" style="width:{{ min($convRate, 100) }}%;background:linear-gradient(90deg,#9333ea,#c084fc);"></div></div>
        </div>
    </div>
</div>

{{-- ══ DEAL PIPELINE FUNNEL ══ --}}
<div class="dash-card dash-pipeline-card">
    <div class="dash-card-header">
        <div>
            <h2 class="dash-card-title">Deal Pipeline</h2>
            <p class="dash-card-desc">Visual deal flow across stages</p>
        </div>
        <a href="{{ route('filament.admin.resources.deals.index') }}" class="dash-link-btn">View Pipeline →</a>
    </div>
    <div class="pipeline-funnel">
        @foreach($dealStages as $stageKey => $stage)
        <div class="pipeline-stage">
            <div class="pipeline-stage-dot" style="background:{{ $stage['color'] }};">{{ $stage['count'] }}</div>
            <span class="pipeline-stage-label">{{ $stage['label'] }}</span>
            <span class="pipeline-stage-val">৳{{ number_format($stage['value']) }}</span>
            @if(!$loop->last)<div class="pipeline-arrow">→</div>@endif
        </div>
        @endforeach
        <div class="pipeline-outcomes">
            <div class="pipeline-won"><span class="pipeline-outcome-num">{{ $closedWonCount }}</span><span class="pipeline-outcome-label">Won</span></div>
            <div class="pipeline-lost"><span class="pipeline-outcome-num">{{ $closedLostCount }}</span><span class="pipeline-outcome-label">Lost</span></div>
        </div>
    </div>
</div>

{{-- ══ MAIN 3-COL ══ --}}
<div class="dash-grid-3">
    {{-- COL 1: Support --}}
    <div class="dash-col">
        <div class="dash-card">
            <div class="dash-card-header">
                <h2 class="dash-card-title">Support Overview</h2>
                <a href="{{ route('filament.admin.resources.support-tickets.index') }}" class="dash-link-sm">View all →</a>
            </div>
            <div class="support-stats">
                <div class="support-stat blue"><span class="support-stat-num">{{ $openTickets }}</span><span class="support-stat-label">Open</span></div>
                <div class="support-stat amber"><span class="support-stat-num">{{ $inProgressTickets }}</span><span class="support-stat-label">In Progress</span></div>
                <div class="support-stat green"><span class="support-stat-num">{{ $resolvedTickets }}</span><span class="support-stat-label">Resolved</span></div>
                <div class="support-stat slate"><span class="support-stat-num">{{ $closedTickets }}</span><span class="support-stat-label">Closed</span></div>
            </div>
        </div>
        <div class="dash-card flex-1">
            <div class="dash-card-header">
                <h2 class="dash-card-title">Recent Tickets</h2>
                <a href="{{ route('filament.admin.resources.support-tickets.create') }}" class="dash-add-btn">+</a>
            </div>
            <div class="dash-list">
                @forelse($recentTickets as $ticket)
                @php $pc = \App\Models\SupportTicket::PRIORITY_COLORS[$ticket->priority] ?? '#94a3b8'; $sc = \App\Models\SupportTicket::STATUS_COLORS[$ticket->status] ?? '#94a3b8'; @endphp
                <a href="{{ route('filament.admin.resources.support-tickets.edit', $ticket->id) }}" class="dash-list-item">
                    <div class="dash-list-bar" style="background:{{ $pc }};"></div>
                    <div class="dash-list-info">
                        <span class="dash-list-title">{{ $ticket->subject }}</span>
                        <span class="dash-list-meta">{{ $ticket->ticket_number }} @if($ticket->client)• {{ \Illuminate\Support\Str::limit($ticket->client->name, 16) }}@endif</span>
                    </div>
                    <span class="dash-badge" style="--badge-c:{{ $sc }};">{{ strtoupper(str_replace('_',' ',$ticket->status)) }}</span>
                </a>
                @empty
                <div class="dash-empty">No tickets yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- COL 2: Leads & Deals --}}
    <div class="dash-col">
        <div class="dash-card flex-1">
            <div class="dash-card-header">
                <h2 class="dash-card-title">Recent Leads</h2>
                <a href="{{ route('filament.admin.resources.leads.index') }}" class="dash-link-sm">View all →</a>
            </div>
            <div class="dash-list">
                @forelse($recentLeads as $lead)
                @php $lsc = \App\Models\Lead::STATUS_COLORS[$lead->status] ?? '#94a3b8'; @endphp
                <div class="dash-list-item">
                    <div class="dash-avatar" style="background:linear-gradient(135deg,#6c5ce7,#a78bfa);">{{ $initials($lead->name) }}</div>
                    <div class="dash-list-info">
                        <span class="dash-list-title">{{ $lead->name }}</span>
                        <span class="dash-list-meta">{{ $lead->email ?? $lead->phone ?? '—' }}</span>
                    </div>
                    <span class="dash-badge" style="--badge-c:{{ $lsc }};">{{ strtoupper($lead->status) }}</span>
                </div>
                @empty
                <div class="dash-empty">No leads yet — <a href="{{ route('filament.admin.resources.leads.create') }}" class="dash-empty-link">Add first lead →</a></div>
                @endforelse
            </div>
        </div>
        <div class="dash-card flex-1">
            <div class="dash-card-header">
                <h2 class="dash-card-title">Recent Deals</h2>
                <a href="{{ route('filament.admin.resources.deals.index') }}" class="dash-link-sm">View all →</a>
            </div>
            <div class="dash-list">
                @forelse($recentDeals as $deal)
                @php $dsc = \App\Models\Deal::STAGE_COLORS[$deal->stage] ?? '#94a3b8'; @endphp
                <div class="dash-list-item">
                    <div class="dash-avatar" style="background:{{ $dsc }}22;color:{{ $dsc }};font-weight:800;">৳</div>
                    <div class="dash-list-info">
                        <span class="dash-list-title">{{ $deal->title }}</span>
                        <span class="dash-list-meta">৳{{ number_format($deal->value) }} • {{ $deal->assignee?->name ?? '—' }}</span>
                    </div>
                    <span class="dash-badge" style="--badge-c:{{ $dsc }};">{{ \App\Models\Deal::STAGE_LABELS[$deal->stage] ?? $deal->stage }}</span>
                </div>
                @empty
                <div class="dash-empty">No deals yet</div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- COL 3: Tasks + Quick Actions --}}
    <div class="dash-col">
        <div class="dash-card dash-task-hero">
            <div class="dash-task-hero-header">
                <span class="dash-task-hero-label">Task Progress</span>
                <span class="dash-task-hero-pct">{{ $taskProgress }}%</span>
            </div>
            <div class="dash-task-hero-bar"><div class="dash-task-hero-fill" style="width:{{ $taskProgress }}%;"></div></div>
            <div class="dash-task-hero-footer">
                <span>{{ $completedTasks }} done</span>
                <span>{{ $pendingTasks + $inProgressTasks }} remaining</span>
            </div>
        </div>

        <div class="dash-card flex-1">
            <div class="dash-card-header">
                <h2 class="dash-card-title">Upcoming Tasks</h2>
                <a href="{{ route('filament.admin.resources.tasks.create') }}" class="dash-add-btn">+</a>
            </div>
            <div class="dash-list">
                @forelse($upcomingTasks as $task)
                <div class="dash-list-item">
                    <div class="dash-task-check {{ $task->due_date && $task->due_date < today() ? 'overdue' : '' }}"></div>
                    <div class="dash-list-info">
                        <span class="dash-list-title">{{ \Illuminate\Support\Str::limit($task->title, 26) }}</span>
                        @if($task->due_date)
                        <span class="dash-list-meta {{ $task->due_date < today() ? 'text-red' : '' }}">📅 {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}</span>
                        @endif
                    </div>
                </div>
                @empty
                <div class="dash-empty">No pending tasks 🎉</div>
                @endforelse
            </div>
            <a href="{{ route('filament.admin.resources.tasks.index') }}" class="dash-card-footer-link">View all tasks →</a>
        </div>

        <div class="dash-card">
            <h2 class="dash-card-title" style="margin-bottom:0.5rem;">Quick Actions</h2>
            <div class="quick-actions">
                <a href="{{ route('filament.admin.resources.leads.create') }}" class="qa-btn"><span class="qa-icon" style="background:#f5f3ff;">👤</span> New Lead</a>
                <a href="{{ route('filament.admin.resources.deals.create') }}" class="qa-btn"><span class="qa-icon" style="background:#eef2ff;">💰</span> New Deal</a>
                <a href="{{ route('filament.admin.resources.support-tickets.create') }}" class="qa-btn"><span class="qa-icon" style="background:#fef2f2;">🎫</span> New Ticket</a>
                <a href="{{ route('filament.admin.resources.tasks.create') }}" class="qa-btn"><span class="qa-icon" style="background:#ecfdf5;">✅</span> New Task</a>
            </div>
        </div>
    </div>
</div>

{{-- ══ BOTTOM ROW: Expiry + Pending ══ --}}
@if($expiringDomains->count() > 0 || $expiringHosting->count() > 0 || $pendingDemos > 0 || $unreadMessages > 0)
<div class="dash-grid-2">
    @if($expiringDomains->count() > 0 || $expiringHosting->count() > 0)
    <div class="dash-card">
        <div class="dash-card-header">
            <div style="display:flex;align-items:center;gap:0.5rem;">
                <div class="dash-alert-icon amber" style="width:28px;height:28px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <h2 class="dash-card-title">Expiring Soon</h2>
            </div>
        </div>
        @foreach($expiringDomains as $d)
        <div class="expiry-row">
            <div><span class="expiry-name">{{ $d->name }}</span><span class="expiry-type">🌐 Domain</span></div>
            <span class="expiry-date {{ $d->domain_expiry->diffInDays(today()) <= 7 ? 'critical' : 'warning' }}">{{ $d->domain_expiry->format('M d') }} ({{ $d->domain_expiry->diffInDays(today()) }}d)</span>
        </div>
        @endforeach
        @foreach($expiringHosting as $h)
        <div class="expiry-row">
            <div><span class="expiry-name">{{ $h->name }}</span><span class="expiry-type">🖥️ Hosting</span></div>
            <span class="expiry-date {{ $h->hosting_expiry->diffInDays(today()) <= 7 ? 'critical' : 'warning' }}">{{ $h->hosting_expiry->format('M d') }} ({{ $h->hosting_expiry->diffInDays(today()) }}d)</span>
        </div>
        @endforeach
    </div>
    @endif

    @if($pendingDemos > 0 || $unreadMessages > 0)
    <div class="dash-card">
        <h2 class="dash-card-title" style="margin-bottom:0.7rem;">Attention Required</h2>
        @if($pendingDemos > 0)
        <a href="{{ route('filament.admin.resources.demo-requests.index') }}" class="attn-item orange">
            <span class="attn-icon">📋</span>
            <div class="attn-info"><strong>{{ $pendingDemos }} Pending Demo{{ $pendingDemos > 1 ? 's' : '' }}</strong><span>Awaiting review</span></div>
            <span class="attn-arrow">→</span>
        </a>
        @endif
        @if($unreadMessages > 0)
        <a href="{{ route('filament.admin.resources.contact-messages.index') }}" class="attn-item blue">
            <span class="attn-icon">✉️</span>
            <div class="attn-info"><strong>{{ $unreadMessages }} Unread Message{{ $unreadMessages > 1 ? 's' : '' }}</strong><span>From contact form</span></div>
            <span class="attn-arrow">→</span>
        </a>
        @endif
    </div>
    @endif
</div>
@endif

</div>

{{-- ═══════════════ PREMIUM CSS ═══════════════ --}}
<style>
/* ── Base ── */
.erp-dash { font-family:'Inter',system-ui,sans-serif; }

/* ── Header ── */
.dash-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; gap:1rem; flex-wrap:wrap; }
.dash-header-left { flex:1; }
.dash-greeting { display:flex; align-items:center; gap:0.7rem; }
.dash-wave { font-size:1.5rem; animation:wave 2s ease-in-out infinite; display:inline-block; transform-origin:70% 70%; }
@keyframes wave { 0%,100%{transform:rotate(0)} 15%{transform:rotate(14deg)} 30%{transform:rotate(-8deg)} 40%{transform:rotate(14deg)} 50%{transform:rotate(-4deg)} 60%{transform:rotate(10deg)} 70%{transform:rotate(0)} }
.dash-title { font-size:1.35rem; font-weight:800; color:#0f172a; letter-spacing:-0.025em; margin:0; line-height:1.3; }
.dash-subtitle { font-size:0.76rem; color:#94a3b8; margin:0.1rem 0 0; font-weight:400; }

/* ── Search ── */
.dash-search-wrap { position:relative; width:340px; }
.dash-search-box { display:flex; align-items:center; background:rgba(255,255,255,0.85); backdrop-filter:blur(12px); border:1.5px solid #e2e8f0; border-radius:12px; padding:0.45rem 0.8rem; gap:0.45rem; transition:all .25s cubic-bezier(.4,0,.2,1); }
.dash-search-box.active { border-color:#6c5ce7; box-shadow:0 0 0 3px rgba(108,92,231,0.08),0 4px 16px rgba(108,92,231,0.06); }
.dash-search-icon { color:#94a3b8; flex-shrink:0; }
.dash-search-input { border:none; outline:none; background:transparent; font-size:0.78rem; color:#0f172a; flex:1; font-family:inherit; }
.dash-search-input::placeholder { color:#cbd5e1; }
.dash-search-spinner { width:14px; height:14px; border:2px solid #e2e8f0; border-top-color:#6c5ce7; border-radius:50%; animation:spin .5s linear infinite; flex-shrink:0; }
.dash-search-kbd { font-size:0.58rem; color:#94a3b8; background:#f1f5f9; border:1px solid #e2e8f0; border-radius:4px; padding:0.1rem 0.35rem; font-family:inherit; }
@keyframes spin { to { transform:rotate(360deg); } }
.dash-search-dropdown { position:absolute; top:calc(100% + 6px); left:0; right:0; background:#fff; border:1.5px solid #e2e8f0; border-radius:14px; box-shadow:0 20px 50px rgba(0,0,0,0.12),0 8px 20px rgba(0,0,0,0.04); z-index:50; max-height:380px; overflow-y:auto; }
.dash-search-empty { padding:2rem; text-align:center; color:#94a3b8; font-size:0.78rem; }
.dash-search-empty svg { margin:0 auto 0.5rem; display:block; }
.dash-search-group { padding:0.45rem 1rem; font-size:0.58rem; font-weight:700; text-transform:uppercase; letter-spacing:0.06em; color:#94a3b8; background:#f8fafc; border-bottom:1px solid #f1f3f5; }
.dash-search-item { display:flex; align-items:center; gap:0.65rem; padding:0.55rem 1rem; text-decoration:none; transition:background .15s; }
.dash-search-item:hover { background:#f8f7ff; }
.dash-search-item-icon { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.7rem; flex-shrink:0; }
.dash-search-item-info { flex:1; min-width:0; }
.dash-search-item-title { display:block; font-size:0.76rem; font-weight:600; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dash-search-item-sub { display:block; font-size:0.62rem; color:#94a3b8; }
.dash-search-item-badge { font-size:0.56rem; font-weight:600; padding:0.12rem 0.45rem; border-radius:999px; white-space:nowrap; }

/* ── Alerts ── */
.dash-alerts { display:flex; gap:0.75rem; margin-bottom:1.25rem; }
.dash-alert { display:flex; align-items:center; gap:0.6rem; border-radius:12px; padding:0.65rem 1rem; text-decoration:none; flex:1; transition:all .2s; position:relative; overflow:hidden; }
.dash-alert strong { font-size:0.78rem; display:block; }
.dash-alert span { font-size:0.64rem; opacity:0.8; }
.dash-alert:hover { transform:translateY(-1px); }
.dash-alert-red { background:linear-gradient(135deg,#fef2f2,#fff1f2); border:1px solid #fecaca; color:#991b1b; }
.dash-alert-red:hover { box-shadow:0 6px 20px rgba(239,68,68,0.12); }
.dash-alert-amber { background:linear-gradient(135deg,#fffbeb,#fef3c7); border:1px solid #fde68a; color:#92400e; }
.dash-alert-amber:hover { box-shadow:0 6px 20px rgba(234,179,8,0.12); }
.dash-alert-icon { width:32px; height:32px; border-radius:9px; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.dash-alert-icon.red { background:linear-gradient(135deg,#ef4444,#f87171); }
.dash-alert-icon.amber { background:linear-gradient(135deg,#f59e0b,#fbbf24); }
.dash-alert-pulse { position:absolute; top:8px; left:8px; width:8px; height:8px; background:#ef4444; border-radius:50%; animation:pulse-ring 1.5s infinite; }
@keyframes pulse-ring { 0%{box-shadow:0 0 0 0 rgba(239,68,68,0.5)} 70%{box-shadow:0 0 0 8px rgba(239,68,68,0)} 100%{box-shadow:0 0 0 0 rgba(239,68,68,0)} }

/* ── KPI Cards ── */
.kpi-row { display:grid; grid-template-columns:repeat(6,1fr); gap:0.85rem; margin-bottom:1.25rem; }
.kpi-card { background:#fff; border-radius:16px; border:1px solid #e8eaed; padding:1.1rem 1.2rem; position:relative; overflow:hidden; transition:all .3s cubic-bezier(.4,0,.2,1); display:flex; flex-direction:column; gap:0.1rem; }
.kpi-card:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(0,0,0,0.08); border-color:transparent; }
.kpi-card::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--kpi-c1,#6c5ce7),var(--kpi-c2,#a78bfa)); opacity:0; transition:opacity .3s; }
.kpi-card:hover::before { opacity:1; }
.kpi-leads { --kpi-c1:#6c5ce7;--kpi-c2:#a78bfa; }
.kpi-clients { --kpi-c1:#059669;--kpi-c2:#34d399; }
.kpi-pipeline { --kpi-c1:#4f46e5;--kpi-c2:#818cf8; }
.kpi-won { --kpi-c1:#ea580c;--kpi-c2:#fb923c; }
.kpi-tickets { --kpi-c1:#dc2626;--kpi-c2:#f87171; }
.kpi-conv { --kpi-c1:#9333ea;--kpi-c2:#c084fc; }
.kpi-icon-wrap { width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,var(--kpi-c1),var(--kpi-c2)); display:flex; align-items:center; justify-content:center; margin-bottom:0.5rem; box-shadow:0 4px 12px color-mix(in srgb, var(--kpi-c1) 25%, transparent); }
.kpi-body { flex:1; }
.kpi-label { font-size:0.64rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:0.15rem; }
.kpi-value { font-size:1.65rem; font-weight:800; color:#0f172a; letter-spacing:-0.02em; line-height:1.2; display:block; }
.kpi-tags { display:flex; gap:0.3rem; margin-top:0.35rem; flex-wrap:wrap; }
.kpi-tag { font-size:0.56rem; font-weight:600; padding:0.1rem 0.35rem; border-radius:999px; }
.kpi-tag.green { color:#059669; background:#ecfdf5; }
.kpi-tag.purple { color:#6c5ce7; background:#f5f3ff; }
.kpi-tag.indigo { color:#4f46e5; background:#eef2ff; }
.kpi-tag.orange { color:#ea580c; background:#fff7ed; }
.kpi-tag.blue { color:#3b82f6; background:#eff6ff; }
.kpi-progress-bar { height:4px; background:#e5e7eb; border-radius:999px; margin-top:0.4rem; overflow:hidden; }
.kpi-progress-fill { height:100%; border-radius:999px; transition:width 1.5s cubic-bezier(.4,0,.2,1); }
.kpi-spark { margin-top:auto; height:24px; opacity:0.7; }
.kpi-spark svg { width:100%; height:100%; }

/* ── Cards ── */
.dash-card { background:#fff; border-radius:16px; border:1px solid #e8eaed; overflow:hidden; transition:box-shadow .25s; }
.dash-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.04); }
.dash-card-header { display:flex; align-items:center; justify-content:space-between; padding:0.9rem 1.2rem; border-bottom:1px solid #f1f3f5; }
.dash-card-title { font-size:0.88rem; font-weight:700; color:#0f172a; margin:0; }
.dash-card-desc { font-size:0.66rem; color:#94a3b8; margin:0.1rem 0 0; }
.dash-link-btn { font-size:0.7rem; font-weight:600; color:#6c5ce7; text-decoration:none; background:rgba(108,92,231,0.06); padding:0.3rem 0.65rem; border-radius:8px; transition:all .15s; }
.dash-link-btn:hover { background:rgba(108,92,231,0.12); }
.dash-link-sm { font-size:0.68rem; font-weight:600; color:#6c5ce7; text-decoration:none; }
.dash-link-sm:hover { text-decoration:underline; }
.dash-add-btn { width:26px; height:26px; background:linear-gradient(135deg,#6c5ce7,#8b5cf6); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff; text-decoration:none; font-size:1.05rem; font-weight:600; transition:all .15s; box-shadow:0 2px 8px rgba(108,92,231,0.25); }
.dash-add-btn:hover { transform:scale(1.08); box-shadow:0 4px 12px rgba(108,92,231,0.35); }

/* ── Pipeline ── */
.dash-pipeline-card { margin-bottom:1.25rem; }
.pipeline-funnel { display:flex; align-items:center; padding:0.8rem 1.2rem; gap:0.4rem; }
.pipeline-stage { flex:1; text-align:center; position:relative; padding:0.6rem 0.3rem; background:#f8fafc; border-radius:10px; border:1px solid #f1f3f5; transition:all .2s; }
.pipeline-stage:hover { background:#f1f0ff; border-color:#ddd6fe; transform:translateY(-2px); }
.pipeline-stage-dot { width:30px; height:30px; border-radius:8px; margin:0 auto 0.35rem; display:flex; align-items:center; justify-content:center; font-size:0.82rem; font-weight:800; color:#fff; box-shadow:0 3px 8px rgba(0,0,0,0.1); }
.pipeline-stage-label { display:block; font-size:0.62rem; font-weight:600; color:#475569; }
.pipeline-stage-val { display:block; font-size:0.56rem; color:#94a3b8; margin-top:0.1rem; }
.pipeline-arrow { position:absolute; right:-12px; top:50%; transform:translateY(-50%); color:#cbd5e1; font-size:0.8rem; z-index:2; font-weight:700; }
.pipeline-outcomes { display:flex; flex-direction:column; gap:0.3rem; margin-left:0.3rem; }
.pipeline-won, .pipeline-lost { text-align:center; padding:0.45rem 0.6rem; border-radius:10px; min-width:60px; }
.pipeline-won { background:#ecfdf5; border:1px solid #a7f3d0; }
.pipeline-lost { background:#fef2f2; border:1px solid #fecaca; }
.pipeline-outcome-num { display:block; font-size:1rem; font-weight:800; }
.pipeline-won .pipeline-outcome-num { color:#059669; }
.pipeline-lost .pipeline-outcome-num { color:#dc2626; }
.pipeline-outcome-label { font-size:0.56rem; font-weight:600; }
.pipeline-won .pipeline-outcome-label { color:#059669; }
.pipeline-lost .pipeline-outcome-label { color:#dc2626; }

/* ── 3-Col Grid ── */
.dash-grid-3 { display:grid; grid-template-columns:1fr 1fr 320px; gap:1.15rem; margin-bottom:1.25rem; }
.dash-col { display:flex; flex-direction:column; gap:1.15rem; }
.flex-1 { flex:1; display:flex; flex-direction:column; }

/* ── Support Stats ── */
.support-stats { display:grid; grid-template-columns:repeat(4,1fr); gap:0.4rem; padding:0.8rem; }
.support-stat { text-align:center; padding:0.55rem 0.3rem; border-radius:10px; transition:transform .2s; }
.support-stat:hover { transform:scale(1.05); }
.support-stat.blue { background:#eff6ff; }
.support-stat.amber { background:#fefce8; }
.support-stat.green { background:#ecfdf5; }
.support-stat.slate { background:#f1f5f9; }
.support-stat-num { display:block; font-size:1.15rem; font-weight:800; }
.support-stat.blue .support-stat-num { color:#3b82f6; }
.support-stat.amber .support-stat-num { color:#ca8a04; }
.support-stat.green .support-stat-num { color:#059669; }
.support-stat.slate .support-stat-num { color:#64748b; }
.support-stat-label { font-size:0.56rem; font-weight:600; }
.support-stat.blue .support-stat-label { color:#3b82f6; }
.support-stat.amber .support-stat-label { color:#ca8a04; }
.support-stat.green .support-stat-label { color:#059669; }
.support-stat.slate .support-stat-label { color:#64748b; }

/* ── Lists ── */
.dash-list { padding:0.35rem; flex:1; }
.dash-list-item { display:flex; align-items:center; gap:0.55rem; padding:0.5rem 0.65rem; border-radius:10px; text-decoration:none; transition:background .15s; border-bottom:1px solid transparent; }
.dash-list-item:hover { background:rgba(108,92,231,0.04); }
.dash-list-bar { width:4px; height:28px; border-radius:3px; flex-shrink:0; }
.dash-avatar { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.6rem; font-weight:700; flex-shrink:0; color:#fff; }
.dash-list-info { flex:1; min-width:0; }
.dash-list-title { display:block; font-size:0.74rem; font-weight:600; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.dash-list-meta { display:block; font-size:0.6rem; color:#94a3b8; margin-top:0.1rem; }
.dash-badge { font-size:0.54rem; font-weight:700; padding:0.12rem 0.45rem; border-radius:999px; white-space:nowrap; color:var(--badge-c); background:color-mix(in srgb, var(--badge-c) 10%, transparent); border:1px solid color-mix(in srgb, var(--badge-c) 20%, transparent); letter-spacing:0.02em; }
.dash-empty { text-align:center; padding:1.8rem 1rem; color:#94a3b8; font-size:0.78rem; }
.dash-empty-link { color:#6c5ce7; text-decoration:none; font-weight:600; }
.dash-card-footer-link { display:block; text-align:center; padding:0.6rem; font-size:0.68rem; font-weight:600; color:#64748b; text-decoration:none; border-top:1px solid #f1f3f5; transition:color .15s; }
.dash-card-footer-link:hover { color:#6c5ce7; }

/* ── Task Hero ── */
.dash-task-hero { background:linear-gradient(135deg,#6c5ce7 0%,#4f46e5 50%,#7c3aed 100%); border:none; padding:1.1rem 1.2rem; color:#fff; }
.dash-task-hero-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:0.55rem; }
.dash-task-hero-label { font-size:0.66rem; font-weight:600; opacity:0.85; text-transform:uppercase; letter-spacing:0.04em; }
.dash-task-hero-pct { font-size:1.15rem; font-weight:800; }
.dash-task-hero-bar { background:rgba(255,255,255,0.15); border-radius:999px; height:5px; overflow:hidden; }
.dash-task-hero-fill { background:#fff; height:100%; border-radius:999px; transition:width 1.5s cubic-bezier(.4,0,.2,1); }
.dash-task-hero-footer { display:flex; justify-content:space-between; margin-top:0.45rem; font-size:0.6rem; opacity:0.7; }
.dash-task-check { width:14px; height:14px; border-radius:4px; border:2px solid #d1d5db; flex-shrink:0; margin-top:2px; transition:border-color .2s; }
.dash-task-check.overdue { border-color:#ef4444; }
.text-red { color:#ef4444 !important; }

/* ── Quick Actions ── */
.quick-actions { display:grid; grid-template-columns:1fr 1fr; gap:0.35rem; }
.qa-btn { display:flex; align-items:center; gap:0.4rem; padding:0.45rem 0.55rem; border-radius:10px; text-decoration:none; font-size:0.72rem; font-weight:500; color:#0f172a; transition:all .15s; }
.qa-btn:hover { background:#f8f7ff; transform:translateX(2px); }
.qa-icon { width:24px; height:24px; border-radius:6px; display:flex; align-items:center; justify-content:center; font-size:0.72rem; flex-shrink:0; }

/* ── Grid 2 ── */
.dash-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.15rem; margin-bottom:1.25rem; }
.expiry-row { display:flex; align-items:center; justify-content:space-between; padding:0.45rem 1.2rem; border-bottom:1px solid #f8fafc; }
.expiry-name { font-size:0.74rem; font-weight:600; color:#0f172a; }
.expiry-type { font-size:0.6rem; color:#94a3b8; margin-left:0.35rem; }
.expiry-date { font-size:0.6rem; font-weight:600; padding:0.12rem 0.45rem; border-radius:999px; }
.expiry-date.warning { background:#fff7ed; color:#d97706; }
.expiry-date.critical { background:#fef2f2; color:#dc2626; }
.attn-item { display:flex; align-items:center; gap:0.6rem; padding:0.6rem 0.75rem; border-radius:10px; text-decoration:none; margin-bottom:0.35rem; transition:all .15s; }
.attn-item:hover { transform:translateX(3px); }
.attn-item.orange { background:#fff7ed; border:1px solid #fed7aa; color:#92400e; }
.attn-item.blue { background:#eff6ff; border:1px solid #bfdbfe; color:#1e40af; }
.attn-icon { font-size:1rem; flex-shrink:0; }
.attn-info { flex:1; }
.attn-info strong { font-size:0.76rem; display:block; }
.attn-info span { font-size:0.6rem; opacity:0.7; }
.attn-arrow { font-size:0.75rem; opacity:0.5; }

/* ── Responsive ── */
@media (max-width:1400px) { .kpi-row { grid-template-columns:repeat(3,1fr); } }
@media (max-width:1200px) {
    .dash-grid-3 { grid-template-columns:1fr; }
    .dash-grid-2 { grid-template-columns:1fr; }
    .dash-search-wrap { width:280px; }
}
@media (max-width:900px) { .kpi-row { grid-template-columns:repeat(2,1fr); } .pipeline-funnel { flex-wrap:wrap; } }
@media (max-width:640px) { .kpi-row { grid-template-columns:1fr; } .dash-header { flex-direction:column; align-items:flex-start; } .dash-search-wrap { width:100%; } }
</style>
