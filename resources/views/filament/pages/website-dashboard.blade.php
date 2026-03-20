@php
    $statusColors = ['pending' => '#f59e0b', 'approved' => '#3b82f6', 'completed' => '#059669', 'rejected' => '#ef4444'];
@endphp

<div class="web-dash" x-data="{ animReady: false }" x-init="setTimeout(() => animReady = true, 100)">

{{-- ══ HEADER ══ --}}
<div class="wd-header">
    <div>
        <h1 class="wd-title">🌐 Website Dashboard</h1>
        <p class="wd-subtitle">{{ now()->format('l, F d, Y') }} — Website performance & engagement analytics</p>
    </div>
    <div class="wd-header-actions">
        <a href="/" target="_blank" class="wd-visit-btn">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
            Visit Website
        </a>
    </div>
</div>

{{-- ══ GA STATUS BANNER ══ --}}
@if(!$gaEnabled || !$gaMeasurementId)
<div class="wd-ga-banner">
    <div class="wd-ga-banner-icon">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/><path d="M12 8v4"/><path d="M12 16h.01"/></svg>
    </div>
    <div class="wd-ga-banner-text">
        <strong>Google Analytics Not Active</strong>
        <span>Enable GA4 tracking in <a href="{{ route('filament.admin.pages.system-settings', ['tab' => '-integrations-tab']) }}">System Settings → Integrations</a> to see real-time visitor data.</span>
    </div>
</div>
@else
<div class="wd-ga-banner active">
    <div class="wd-ga-banner-icon active">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
    </div>
    <div class="wd-ga-banner-text">
        <strong>Google Analytics Active</strong>
        <span>Tracking ID: <code>{{ $gaMeasurementId }}</code> — <a href="https://analytics.google.com" target="_blank">Open GA Dashboard →</a></span>
    </div>
</div>
@endif

{{-- ══ KPI ROW ══ --}}
<div class="wd-kpi-row">
    <div class="wd-kpi" style="--kc1:#f59e0b;--kc2:#fbbf24;">
        <div class="wd-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg></div>
        <div class="wd-kpi-body">
            <span class="wd-kpi-label">Demo Requests</span>
            <span class="wd-kpi-value">{{ $totalDemos }}</span>
            <div class="wd-kpi-tags">
                @if($todayDemos > 0)<span class="wd-kpi-tag green">+{{ $todayDemos }} today</span>@endif
                <span class="wd-kpi-tag amber">{{ $weekDemos }}/wk</span>
            </div>
        </div>
    </div>

    <div class="wd-kpi" style="--kc1:#ef4444;--kc2:#f87171;">
        <div class="wd-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg></div>
        <div class="wd-kpi-body">
            <span class="wd-kpi-label">Pending Demos</span>
            <span class="wd-kpi-value">{{ $pendingDemos }}</span>
            <div class="wd-kpi-tags"><span class="wd-kpi-tag red">Needs review</span></div>
        </div>
    </div>

    <div class="wd-kpi" style="--kc1:#3b82f6;--kc2:#60a5fa;">
        <div class="wd-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg></div>
        <div class="wd-kpi-body">
            <span class="wd-kpi-label">Contact Messages</span>
            <span class="wd-kpi-value">{{ $totalMessages }}</span>
            <div class="wd-kpi-tags">
                @if($unreadMessages > 0)<span class="wd-kpi-tag red">{{ $unreadMessages }} unread</span>@endif
                <span class="wd-kpi-tag blue">{{ $weekMessages }}/wk</span>
            </div>
        </div>
    </div>

    <div class="wd-kpi" style="--kc1:#8b5cf6;--kc2:#a78bfa;">
        <div class="wd-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg></div>
        <div class="wd-kpi-body">
            <span class="wd-kpi-label">Active Modules</span>
            <span class="wd-kpi-value">{{ $activeModules }}</span>
            <div class="wd-kpi-tags"><span class="wd-kpi-tag purple">{{ $modulesWithVideo }} with videos</span></div>
        </div>
    </div>

    <div class="wd-kpi" style="--kc1:#059669;--kc2:#34d399;">
        <div class="wd-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/><polyline points="17 6 23 6 23 12"/></svg></div>
        <div class="wd-kpi-body">
            <span class="wd-kpi-label">Conversion Rate</span>
            <span class="wd-kpi-value">{{ $demoConvRate }}%</span>
            <div class="wd-kpi-progress"><div class="wd-kpi-progress-fill" style="width:{{ min($demoConvRate, 100) }}%;background:linear-gradient(90deg,#059669,#34d399);"></div></div>
        </div>
    </div>

    <div class="wd-kpi" style="--kc1:#ec4899;--kc2:#f472b6;">
        <div class="wd-kpi-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2" ry="2"/></svg></div>
        <div class="wd-kpi-body">
            <span class="wd-kpi-label">Tutorial Videos</span>
            <span class="wd-kpi-value">{{ $totalVideos }}</span>
            <div class="wd-kpi-tags"><span class="wd-kpi-tag pink">{{ $modulesWithVideo }} modules</span></div>
        </div>
    </div>
</div>

{{-- ══ CHARTS ROW ══ --}}
<div class="wd-grid-2">
    {{-- Demo Requests Trend --}}
    <div class="wd-card">
        <div class="wd-card-header">
            <div>
                <h2 class="wd-card-title">Demo Requests Trend</h2>
                <p class="wd-card-desc">Last 14 days — {{ $monthDemos }} this month</p>
            </div>
            <a href="{{ route('filament.admin.resources.demo-requests.index') }}" class="wd-link-btn">View All →</a>
        </div>
        <div class="wd-chart-area">
            @php $maxDemo = max(1, max($demoTrendFull)); @endphp
            <div class="wd-bar-chart">
                @foreach($demoTrendFull as $date => $count)
                <div class="wd-bar-col" title="{{ \Carbon\Carbon::parse($date)->format('M d') }}: {{ $count }} demos">
                    <div class="wd-bar" style="height:{{ ($count / $maxDemo) * 100 }}%;background:linear-gradient(180deg,#f59e0b,#fbbf24);" x-bind:style="animReady ? 'height:{{ ($count / $maxDemo) * 100 }}%' : 'height:0%'"></div>
                    <span class="wd-bar-label">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Demo Status Breakdown --}}
    <div class="wd-card">
        <div class="wd-card-header">
            <h2 class="wd-card-title">Demo Request Status</h2>
        </div>
        <div class="wd-status-grid">
            @php
                $statuses = [
                    ['key' => 'pending', 'label' => 'Pending', 'count' => $pendingDemos, 'color' => '#f59e0b', 'icon' => '⏳', 'bg' => '#fffbeb'],
                    ['key' => 'approved', 'label' => 'Approved', 'count' => $approvedDemos, 'color' => '#3b82f6', 'icon' => '✅', 'bg' => '#eff6ff'],
                    ['key' => 'completed', 'label' => 'Completed', 'count' => $completedDemos, 'color' => '#059669', 'icon' => '🎉', 'bg' => '#ecfdf5'],
                    ['key' => 'rejected', 'label' => 'Rejected', 'count' => $rejectedDemos, 'color' => '#ef4444', 'icon' => '❌', 'bg' => '#fef2f2'],
                ];
            @endphp
            @foreach($statuses as $s)
            <div class="wd-status-card" style="background:{{ $s['bg'] }};border-color:{{ $s['color'] }}20;">
                <span class="wd-status-icon">{{ $s['icon'] }}</span>
                <span class="wd-status-num" style="color:{{ $s['color'] }};">{{ $s['count'] }}</span>
                <span class="wd-status-label" style="color:{{ $s['color'] }};">{{ $s['label'] }}</span>
                @if($totalDemos > 0)
                <div class="wd-status-bar"><div style="width:{{ round(($s['count'] / $totalDemos) * 100) }}%;background:{{ $s['color'] }};"></div></div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
</div>

{{-- ══ MIDDLE ROW ══ --}}
<div class="wd-grid-3">
    {{-- Messages Trend --}}
    <div class="wd-card">
        <div class="wd-card-header">
            <h2 class="wd-card-title">Messages (14 days)</h2>
            <a href="{{ route('filament.admin.resources.contact-messages.index') }}" class="wd-link-sm">View →</a>
        </div>
        <div class="wd-chart-area small">
            @php $maxMsg = max(1, max($messageTrendFull)); @endphp
            <div class="wd-bar-chart">
                @foreach($messageTrendFull as $date => $count)
                <div class="wd-bar-col">
                    <div class="wd-bar" style="background:linear-gradient(180deg,#3b82f6,#93c5fd);" x-bind:style="animReady ? 'height:{{ ($count / $maxMsg) * 100 }}%' : 'height:0%'"></div>
                    <span class="wd-bar-label">{{ \Carbon\Carbon::parse($date)->format('d') }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Most Requested Modules --}}
    <div class="wd-card">
        <div class="wd-card-header">
            <h2 class="wd-card-title">Most Requested Modules</h2>
        </div>
        <div class="wd-module-list">
            @php $maxPop = max(1, count($modulePopularity) > 0 ? max($modulePopularity) : 1); @endphp
            @forelse($modulePopularity as $modName => $reqCount)
            <div class="wd-module-item">
                <span class="wd-module-name">{{ $modName }}</span>
                <div class="wd-module-bar-wrap">
                    <div class="wd-module-bar" style="width:{{ ($reqCount / $maxPop) * 100 }}%;"></div>
                </div>
                <span class="wd-module-count">{{ $reqCount }}</span>
            </div>
            @empty
            <div class="wd-empty">No module request data yet</div>
            @endforelse
        </div>
    </div>

    {{-- Institution Types --}}
    <div class="wd-card">
        <div class="wd-card-header">
            <h2 class="wd-card-title">Institution Types</h2>
        </div>
        <div class="wd-donut-list">
            @php
                $typeColors = ['School' => '#3b82f6', 'College' => '#8b5cf6', 'School & College' => '#059669', 'University' => '#f59e0b', 'Madrasa' => '#ec4899', 'Other' => '#94a3b8'];
                $totalInst = max(1, array_sum($institutionTypes));
            @endphp
            @forelse($institutionTypes as $type => $cnt)
            @php $tc = $typeColors[$type] ?? '#94a3b8'; @endphp
            <div class="wd-donut-item">
                <div class="wd-donut-dot" style="background:{{ $tc }};"></div>
                <span class="wd-donut-label">{{ $type }}</span>
                <span class="wd-donut-pct" style="color:{{ $tc }};">{{ round(($cnt / $totalInst) * 100) }}%</span>
                <span class="wd-donut-cnt">{{ $cnt }}</span>
            </div>
            @empty
            <div class="wd-empty">No data yet</div>
            @endforelse
        </div>
    </div>
</div>

{{-- ══ LATEST DATA ══ --}}
<div class="wd-grid-2">
    {{-- Recent Demo Requests --}}
    <div class="wd-card">
        <div class="wd-card-header">
            <h2 class="wd-card-title">Recent Demo Requests</h2>
            <a href="{{ route('filament.admin.resources.demo-requests.create') }}" class="wd-add-btn">+</a>
        </div>
        <div class="wd-list">
            @forelse($recentDemos as $demo)
            @php $sc = $statusColors[$demo->status] ?? '#94a3b8'; @endphp
            <a href="{{ route('filament.admin.resources.demo-requests.edit', $demo->id) }}" class="wd-list-item">
                <div class="wd-list-bar" style="background:{{ $sc }};"></div>
                <div class="wd-list-info">
                    <span class="wd-list-title">{{ $demo->contact_name }}</span>
                    <span class="wd-list-meta">{{ $demo->institution_name ?? $demo->email }} • {{ $demo->created_at->diffForHumans() }}</span>
                </div>
                <span class="wd-badge" style="--bc:{{ $sc }};">{{ strtoupper($demo->status) }}</span>
            </a>
            @empty
            <div class="wd-empty">No demo requests yet</div>
            @endforelse
        </div>
    </div>

    {{-- Recent Contact Messages --}}
    <div class="wd-card">
        <div class="wd-card-header">
            <h2 class="wd-card-title">Recent Messages</h2>
            <a href="{{ route('filament.admin.resources.contact-messages.index') }}" class="wd-link-sm">View all →</a>
        </div>
        <div class="wd-list">
            @forelse($recentMessages as $msg)
            @php $mc = $msg->status === 'new' ? '#ef4444' : '#059669'; @endphp
            <div class="wd-list-item">
                <div class="wd-list-avatar" style="background:{{ $msg->status === 'new' ? '#fef2f2' : '#ecfdf5' }};color:{{ $mc }};">
                    {{ strtoupper(substr($msg->name, 0, 1)) }}
                </div>
                <div class="wd-list-info">
                    <span class="wd-list-title">{{ $msg->name }}</span>
                    <span class="wd-list-meta">{{ \Illuminate\Support\Str::limit($msg->subject ?? $msg->message, 40) }} • {{ $msg->created_at->diffForHumans() }}</span>
                </div>
                <span class="wd-badge" style="--bc:{{ $mc }};">{{ $msg->status === 'new' ? 'UNREAD' : 'READ' }}</span>
            </div>
            @empty
            <div class="wd-empty">No messages yet</div>
            @endforelse
        </div>
    </div>
</div>

</div>

{{-- ═══════════════ WEBSITE DASHBOARD CSS ═══════════════ --}}
<style>
.web-dash { font-family:'Inter',system-ui,sans-serif; }

/* ── Header ── */
.wd-header { display:flex; align-items:center; justify-content:space-between; margin-bottom:1.5rem; flex-wrap:wrap; gap:1rem; }
.wd-title { font-size:1.35rem; font-weight:800; color:#0f172a; margin:0; letter-spacing:-0.02em; }
.wd-subtitle { font-size:0.76rem; color:#94a3b8; margin:0.1rem 0 0; }
.wd-header-actions { display:flex; gap:0.5rem; }
.wd-visit-btn { display:inline-flex; align-items:center; gap:0.4rem; padding:0.45rem 1rem; background:linear-gradient(135deg,#6c5ce7,#8b5cf6); color:#fff; border-radius:10px; font-size:0.75rem; font-weight:600; text-decoration:none; transition:all .2s; box-shadow:0 2px 10px rgba(108,92,231,0.25); }
.wd-visit-btn:hover { transform:translateY(-2px); box-shadow:0 6px 20px rgba(108,92,231,0.35); }

/* ── GA Banner ── */
.wd-ga-banner { display:flex; align-items:center; gap:0.75rem; padding:0.75rem 1.2rem; border-radius:12px; margin-bottom:1.25rem; background:#fffbeb; border:1px solid #fde68a; }
.wd-ga-banner.active { background:#ecfdf5; border-color:#a7f3d0; }
.wd-ga-banner-icon { width:36px; height:36px; border-radius:10px; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#f59e0b,#fbbf24); color:#fff; flex-shrink:0; }
.wd-ga-banner-icon.active { background:linear-gradient(135deg,#059669,#34d399); }
.wd-ga-banner-text strong { font-size:0.82rem; color:#0f172a; display:block; }
.wd-ga-banner-text span { font-size:0.7rem; color:#64748b; }
.wd-ga-banner-text a { color:#6c5ce7; font-weight:600; text-decoration:none; }
.wd-ga-banner-text code { background:#f1f5f9; padding:0.1rem 0.4rem; border-radius:4px; font-size:0.68rem; font-weight:600; color:#0f172a; }

/* ── KPI Row ── */
.wd-kpi-row { display:grid; grid-template-columns:repeat(6,1fr); gap:0.85rem; margin-bottom:1.25rem; }
.wd-kpi { background:#fff; border-radius:16px; border:1px solid #e8eaed; padding:1rem 1.1rem; position:relative; overflow:hidden; transition:all .3s cubic-bezier(.4,0,.2,1); }
.wd-kpi:hover { transform:translateY(-3px); box-shadow:0 12px 32px rgba(0,0,0,0.08); border-color:transparent; }
.wd-kpi::before { content:''; position:absolute; top:0; left:0; right:0; height:3px; background:linear-gradient(90deg,var(--kc1),var(--kc2)); opacity:0; transition:opacity .3s; }
.wd-kpi:hover::before { opacity:1; }
.wd-kpi-icon { width:36px; height:36px; border-radius:10px; background:linear-gradient(135deg,var(--kc1),var(--kc2)); display:flex; align-items:center; justify-content:center; margin-bottom:0.5rem; box-shadow:0 4px 12px color-mix(in srgb,var(--kc1) 25%,transparent); }
.wd-kpi-label { font-size:0.64rem; font-weight:600; color:#94a3b8; text-transform:uppercase; letter-spacing:0.05em; display:block; margin-bottom:0.1rem; }
.wd-kpi-value { font-size:1.65rem; font-weight:800; color:#0f172a; letter-spacing:-0.02em; line-height:1.2; display:block; }
.wd-kpi-tags { display:flex; gap:0.3rem; margin-top:0.35rem; flex-wrap:wrap; }
.wd-kpi-tag { font-size:0.56rem; font-weight:600; padding:0.1rem 0.35rem; border-radius:999px; }
.wd-kpi-tag.green { color:#059669; background:#ecfdf5; }
.wd-kpi-tag.amber { color:#d97706; background:#fffbeb; }
.wd-kpi-tag.red { color:#ef4444; background:#fef2f2; }
.wd-kpi-tag.blue { color:#3b82f6; background:#eff6ff; }
.wd-kpi-tag.purple { color:#8b5cf6; background:#f5f3ff; }
.wd-kpi-tag.pink { color:#ec4899; background:#fdf2f8; }
.wd-kpi-progress { height:4px; background:#e5e7eb; border-radius:999px; margin-top:0.4rem; overflow:hidden; }
.wd-kpi-progress-fill { height:100%; border-radius:999px; transition:width 1.5s cubic-bezier(.4,0,.2,1); }

/* ── Cards ── */
.wd-card { background:#fff; border-radius:16px; border:1px solid #e8eaed; overflow:hidden; transition:box-shadow .25s; }
.wd-card:hover { box-shadow:0 4px 16px rgba(0,0,0,0.04); }
.wd-card-header { display:flex; align-items:center; justify-content:space-between; padding:0.9rem 1.2rem; border-bottom:1px solid #f1f3f5; }
.wd-card-title { font-size:0.88rem; font-weight:700; color:#0f172a; margin:0; }
.wd-card-desc { font-size:0.66rem; color:#94a3b8; margin:0.1rem 0 0; }
.wd-link-btn { font-size:0.7rem; font-weight:600; color:#6c5ce7; text-decoration:none; background:rgba(108,92,231,0.06); padding:0.3rem 0.65rem; border-radius:8px; transition:all .15s; }
.wd-link-btn:hover { background:rgba(108,92,231,0.12); }
.wd-link-sm { font-size:0.68rem; font-weight:600; color:#6c5ce7; text-decoration:none; }
.wd-add-btn { width:26px; height:26px; background:linear-gradient(135deg,#6c5ce7,#8b5cf6); border-radius:8px; display:flex; align-items:center; justify-content:center; color:#fff; text-decoration:none; font-size:1rem; font-weight:600; transition:all .15s; box-shadow:0 2px 8px rgba(108,92,231,0.25); }

/* ── Bar Chart ── */
.wd-chart-area { padding:1rem 1.2rem 0.5rem; }
.wd-chart-area.small { padding:0.7rem 1rem 0.3rem; }
.wd-bar-chart { display:flex; align-items:flex-end; gap:4px; height:120px; }
.wd-chart-area.small .wd-bar-chart { height:90px; }
.wd-bar-col { flex:1; display:flex; flex-direction:column; align-items:center; height:100%; justify-content:flex-end; }
.wd-bar { width:100%; min-height:2px; border-radius:4px 4px 0 0; transition:height 1s cubic-bezier(.4,0,.2,1); }
.wd-bar-label { font-size:0.52rem; color:#94a3b8; margin-top:4px; font-weight:600; }

/* ── Status Grid ── */
.wd-status-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:0.6rem; padding:1rem 1.2rem; }
.wd-status-card { text-align:center; padding:1rem 0.5rem; border-radius:12px; border:1px solid; transition:transform .2s; }
.wd-status-card:hover { transform:scale(1.03); }
.wd-status-icon { font-size:1.1rem; display:block; margin-bottom:0.25rem; }
.wd-status-num { font-size:1.5rem; font-weight:800; display:block; line-height:1.2; }
.wd-status-label { font-size:0.62rem; font-weight:600; text-transform:uppercase; letter-spacing:0.04em; }
.wd-status-bar { height:3px; background:rgba(0,0,0,0.06); border-radius:999px; margin-top:0.5rem; overflow:hidden; }
.wd-status-bar > div { height:100%; border-radius:999px; transition:width 1s ease; }

/* ── Module List ── */
.wd-module-list { padding:0.7rem 1.2rem; }
.wd-module-item { display:flex; align-items:center; gap:0.6rem; padding:0.4rem 0; border-bottom:1px solid #f8fafc; }
.wd-module-name { font-size:0.72rem; font-weight:500; color:#334155; width:140px; flex-shrink:0; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.wd-module-bar-wrap { flex:1; height:6px; background:#f1f5f9; border-radius:999px; overflow:hidden; }
.wd-module-bar { height:100%; background:linear-gradient(90deg,#8b5cf6,#c4b5fd); border-radius:999px; transition:width 1s ease; }
.wd-module-count { font-size:0.68rem; font-weight:700; color:#6c5ce7; width:24px; text-align:right; }

/* ── Donut List ── */
.wd-donut-list { padding:0.7rem 1.2rem; }
.wd-donut-item { display:flex; align-items:center; gap:0.6rem; padding:0.5rem 0; border-bottom:1px solid #f8fafc; }
.wd-donut-dot { width:10px; height:10px; border-radius:3px; flex-shrink:0; }
.wd-donut-label { font-size:0.74rem; font-weight:500; color:#334155; flex:1; }
.wd-donut-pct { font-size:0.72rem; font-weight:700; }
.wd-donut-cnt { font-size:0.64rem; color:#94a3b8; font-weight:600; width:28px; text-align:right; }

/* ── Lists ── */
.wd-list { padding:0.35rem; }
.wd-list-item { display:flex; align-items:center; gap:0.55rem; padding:0.5rem 0.65rem; border-radius:10px; text-decoration:none; transition:background .15s; }
.wd-list-item:hover { background:rgba(108,92,231,0.04); }
.wd-list-bar { width:4px; height:28px; border-radius:3px; flex-shrink:0; }
.wd-list-avatar { width:30px; height:30px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.7rem; font-weight:700; flex-shrink:0; }
.wd-list-info { flex:1; min-width:0; }
.wd-list-title { display:block; font-size:0.74rem; font-weight:600; color:#0f172a; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
.wd-list-meta { display:block; font-size:0.6rem; color:#94a3b8; margin-top:0.1rem; }
.wd-badge { font-size:0.54rem; font-weight:700; padding:0.12rem 0.45rem; border-radius:999px; white-space:nowrap; color:var(--bc); background:color-mix(in srgb,var(--bc) 10%,transparent); border:1px solid color-mix(in srgb,var(--bc) 20%,transparent); letter-spacing:0.02em; }
.wd-empty { text-align:center; padding:1.8rem 1rem; color:#94a3b8; font-size:0.78rem; }

/* ── Grids ── */
.wd-grid-2 { display:grid; grid-template-columns:1fr 1fr; gap:1.15rem; margin-bottom:1.25rem; }
.wd-grid-3 { display:grid; grid-template-columns:1fr 1fr 1fr; gap:1.15rem; margin-bottom:1.25rem; }

/* ── Responsive ── */
@media (max-width:1400px) { .wd-kpi-row { grid-template-columns:repeat(3,1fr); } }
@media (max-width:1200px) { .wd-grid-3 { grid-template-columns:1fr; } .wd-grid-2 { grid-template-columns:1fr; } }
@media (max-width:900px) { .wd-kpi-row { grid-template-columns:repeat(2,1fr); } }
@media (max-width:640px) { .wd-kpi-row { grid-template-columns:1fr; } .wd-header { flex-direction:column; align-items:flex-start; } }
</style>
