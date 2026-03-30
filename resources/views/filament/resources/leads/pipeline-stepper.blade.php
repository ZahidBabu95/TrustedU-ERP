@php
    $steps = [
        ['key' => 'new',         'label' => 'New',         'icon' => '✨', 'desc' => 'নতুন লিড'],
        ['key' => 'contacted',   'label' => 'Contacted',   'icon' => '📞', 'desc' => 'যোগাযোগ হয়েছে'],
        ['key' => 'qualified',   'label' => 'Qualified',   'icon' => '✅', 'desc' => 'যোগ্যতা যাচাই সম্পন্ন'],
        ['key' => 'proposal',    'label' => 'Proposal',    'icon' => '📄', 'desc' => 'প্রস্তাব পাঠানো হয়েছে'],
        ['key' => 'negotiation', 'label' => 'Negotiation', 'icon' => '🤝', 'desc' => 'আলোচনা চলছে'],
        ['key' => 'won',         'label' => 'Won',         'icon' => '🏆', 'desc' => 'সফলভাবে সম্পন্ন'],
    ];

    $currentStatus = $lead->status;
    $currentIndex = collect($steps)->search(fn($s) => $s['key'] === $currentStatus);
    if ($currentIndex === false) $currentIndex = -1;
    $isLost = $currentStatus === 'lost';
@endphp

<div class="crm-pipeline-stepper">
    {{-- Lead Info Header --}}
    <div class="lead-info-header">
        <div class="lead-avatar">
            {{ strtoupper(substr($lead->name, 0, 2)) }}
        </div>
        <div class="lead-meta">
            <h3 class="lead-title">{{ $lead->name }}</h3>
            <p class="lead-subtitle">
                @if($lead->institute_name) {{ $lead->institute_name }} · @endif
                @if($lead->email) {{ $lead->email }} @endif
                @if($lead->phone) · {{ $lead->phone }} @endif
            </p>
        </div>
        @if($lead->interest_level)
            <span class="interest-badge interest-{{ $lead->interest_level }}">
                {{ $lead->interest_level === 'hot' ? '🔥 Hot' : ($lead->interest_level === 'warm' ? '🌤 Warm' : '❄️ Cold') }}
            </span>
        @endif
    </div>

    @if($isLost)
        <div class="lost-banner">
            <span class="lost-icon">❌</span>
            <div>
                <strong>এই লিড হারিয়ে গেছে</strong>
                @if($lead->lost_reason)<p style="margin:4px 0 0;opacity:.8;">{{ $lead->lost_reason }}</p>@endif
            </div>
        </div>
    @endif

    {{-- Stepper --}}
    <div class="stepper-container">
        @foreach($steps as $index => $step)
            @php
                $isDone = $index < $currentIndex;
                $isCurrent = $index === $currentIndex;
                $isFuture = $index > $currentIndex;
            @endphp
            <div class="step-wrapper">
                <div class="step-item {{ $isDone ? 'done' : '' }} {{ $isCurrent ? 'current' : '' }} {{ $isFuture ? 'future' : '' }} {{ $isLost ? 'lost-state' : '' }}">
                    <div class="step-circle">
                        @if($isDone)
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                        @elseif($isCurrent)
                            <span class="step-emoji">{{ $step['icon'] }}</span>
                        @else
                            <span class="step-number">{{ $index + 1 }}</span>
                        @endif
                    </div>
                    <div class="step-label">{{ $step['label'] }}</div>
                    <div class="step-desc">{{ $step['desc'] }}</div>
                </div>
                @if(!$loop->last)
                    <div class="step-line {{ $isDone ? 'done' : '' }} {{ $isCurrent ? 'current-line' : '' }}"></div>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Current Status Info --}}
    @if(!$isLost)
        <div class="status-info-box">
            <div class="status-current">
                <span class="status-dot {{ $currentStatus }}"></span>
                <span>বর্তমান স্ট্যাটাস: <strong>{{ \App\Models\Lead::STATUS_LABELS[$currentStatus] ?? ucfirst($currentStatus) }}</strong></span>
            </div>
            @if($currentIndex < count($steps) - 1)
                <div class="status-next">
                    → পরবর্তী: <strong>{{ $steps[$currentIndex + 1]['label'] ?? '—' }}</strong>
                </div>
            @else
                <div class="status-complete">🎉 সকল ধাপ সম্পন্ন!</div>
            @endif
        </div>
    @endif

    {{-- Additional Info --}}
    <div class="lead-details-grid">
        <div class="detail-item">
            <span class="detail-label">সোর্স</span>
            <span class="detail-value">{{ \App\Models\Lead::SOURCE_LABELS[$lead->source] ?? $lead->source }}</span>
        </div>
        @if($lead->value)
            <div class="detail-item">
                <span class="detail-label">ভ্যালু</span>
                <span class="detail-value">৳{{ number_format($lead->value) }}</span>
            </div>
        @endif
        @if($lead->assignee)
            <div class="detail-item">
                <span class="detail-label">অ্যাসাইনড</span>
                <span class="detail-value">{{ $lead->assignee->name }}</span>
            </div>
        @endif
        <div class="detail-item">
            <span class="detail-label">তৈরি</span>
            <span class="detail-value">{{ $lead->created_at->diffForHumans() }}</span>
        </div>
    </div>
</div>

<style>
    .crm-pipeline-stepper {
        padding: 0 4px;
    }

    /* Lead Info Header */
    .lead-info-header {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 16px;
        background: linear-gradient(135deg, #f0f4ff 0%, #e8f0fe 100%);
        border-radius: 12px;
        margin-bottom: 20px;
    }
    .dark .lead-info-header {
        background: linear-gradient(135deg, #1e293b 0%, #1a2332 100%);
    }
    .lead-avatar {
        width: 48px; height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 16px;
        flex-shrink: 0;
    }
    .lead-meta { flex: 1; }
    .lead-title { margin: 0; font-size: 16px; font-weight: 700; color: #1e293b; }
    .dark .lead-title { color: #e2e8f0; }
    .lead-subtitle { margin: 2px 0 0; font-size: 12px; color: #64748b; }
    .dark .lead-subtitle { color: #94a3b8; }
    .interest-badge {
        padding: 4px 12px; border-radius: 20px;
        font-size: 12px; font-weight: 600;
    }
    .interest-hot { background: #fef2f2; color: #dc2626; }
    .interest-warm { background: #fffbeb; color: #d97706; }
    .interest-cold { background: #f0f4ff; color: #6b7280; }
    .dark .interest-hot { background: #451a1a; }
    .dark .interest-warm { background: #451a00; }
    .dark .interest-cold { background: #1e293b; }

    /* Lost Banner */
    .lost-banner {
        display: flex; align-items: center; gap: 10px;
        padding: 12px 16px; border-radius: 10px;
        background: #fef2f2; border: 1px solid #fecaca;
        margin-bottom: 20px; color: #991b1b;
    }
    .dark .lost-banner { background: #451a1a; border-color: #7f1d1d; color: #fca5a5; }
    .lost-icon { font-size: 24px; }

    /* Stepper Container */
    .stepper-container {
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 20px 0;
        overflow-x: auto;
    }
    .step-wrapper {
        display: flex;
        align-items: flex-start;
        flex: 1;
        min-width: 0;
    }
    .step-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        min-width: 80px;
        position: relative;
    }

    /* Step Circle */
    .step-circle {
        width: 44px; height: 44px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700;
        transition: all 0.3s ease;
        position: relative;
        z-index: 2;
    }
    .step-item.done .step-circle {
        background: linear-gradient(135deg, #22c55e, #16a34a);
        box-shadow: 0 4px 12px rgba(34,197,94,.35);
    }
    .step-item.current .step-circle {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        box-shadow: 0 4px 16px rgba(99,102,241,.45);
        animation: circlePulse 2s infinite;
    }
    .step-item.future .step-circle {
        background: #e2e8f0;
        color: #94a3b8;
    }
    .dark .step-item.future .step-circle {
        background: #334155;
        color: #64748b;
    }
    .step-item.lost-state .step-circle {
        opacity: 0.4;
    }

    .step-emoji { font-size: 20px; }
    .step-number { font-size: 14px; }

    /* Step Labels */
    .step-label {
        font-size: 11px;
        font-weight: 600;
        text-align: center;
        color: #475569;
    }
    .step-item.done .step-label { color: #16a34a; }
    .step-item.current .step-label { color: #6366f1; font-weight: 700; }
    .step-item.future .step-label { color: #94a3b8; }
    .dark .step-label { color: #94a3b8; }
    .dark .step-item.done .step-label { color: #4ade80; }
    .dark .step-item.current .step-label { color: #a5b4fc; }

    .step-desc {
        font-size: 9px;
        color: #94a3b8;
        text-align: center;
        max-width: 85px;
    }
    .step-item.current .step-desc { color: #818cf8; }

    /* Step Line */
    .step-line {
        flex: 1;
        height: 3px;
        background: #e2e8f0;
        margin-top: 20px;
        min-width: 10px;
        border-radius: 2px;
        transition: background 0.3s;
    }
    .dark .step-line { background: #334155; }
    .step-line.done {
        background: linear-gradient(90deg, #22c55e, #4ade80);
    }
    .step-line.current-line {
        background: linear-gradient(90deg, #6366f1 40%, #e2e8f0 100%);
    }
    .dark .step-line.current-line {
        background: linear-gradient(90deg, #6366f1 40%, #334155 100%);
    }

    /* Status Info Box */
    .status-info-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 12px 16px;
        background: #f8fafc;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        margin-bottom: 16px;
        font-size: 13px;
    }
    .dark .status-info-box { background: #1e293b; border-color: #334155; color: #e2e8f0; }
    .status-current { display: flex; align-items: center; gap: 8px; }
    .status-dot {
        width: 10px; height: 10px; border-radius: 50%;
        display: inline-block;
    }
    .status-dot.new { background: #94a3b8; }
    .status-dot.contacted { background: #3b82f6; }
    .status-dot.qualified { background: #6366f1; }
    .status-dot.proposal { background: #f59e0b; }
    .status-dot.negotiation { background: #8b5cf6; }
    .status-dot.won { background: #22c55e; }
    .status-next { color: #6366f1; font-weight: 500; }
    .status-complete { color: #16a34a; font-weight: 600; }

    /* Details Grid */
    .lead-details-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(100px, 1fr));
        gap: 8px;
    }
    .detail-item {
        padding: 10px 12px;
        background: #f8fafc;
        border-radius: 8px;
        text-align: center;
    }
    .dark .detail-item { background: #1e293b; }
    .detail-label {
        font-size: 10px;
        color: #94a3b8;
        display: block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 2px;
    }
    .detail-value {
        font-size: 13px;
        font-weight: 600;
        color: #334155;
    }
    .dark .detail-value { color: #e2e8f0; }

    @keyframes circlePulse {
        0%, 100% { box-shadow: 0 4px 16px rgba(99,102,241,.45), 0 0 0 0 rgba(99,102,241,.3); }
        50% { box-shadow: 0 4px 16px rgba(99,102,241,.45), 0 0 0 10px rgba(99,102,241,0); }
    }
</style>
