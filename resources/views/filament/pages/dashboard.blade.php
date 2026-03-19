@php
    use App\Models\Lead;
    use App\Models\Client;
    use App\Models\Task;
    use App\Models\DemoRequest;
    use App\Models\SupportTicket;
    use App\Models\ContactMessage;

    $totalLeads    = Lead::count();
    $totalClients  = Client::where('is_active', true)->count();
    $revenue       = $totalClients * 4500;
    $pendingDemo   = DemoRequest::where('status', 'pending')->count();
    $convRate      = $totalLeads > 0 ? round(($totalClients / $totalLeads) * 100, 1) : 0;

    $recentLeads   = Lead::latest()->limit(5)->get();
    $upcomingTasks = Task::where('status', '!=', 'completed')->latest()->limit(5)->get();
    $completedTasks = Task::where('status', 'completed')->count();
    $totalTasks    = Task::count();
    $taskProgress  = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;

    $initials = fn(string $name): string => collect(explode(' ', $name))
        ->map(fn($w) => strtoupper($w[0] ?? ''))
        ->take(2)->implode('');
@endphp

<div class="premium-dashboard" style="font-family:'Inter',sans-serif;">

    {{-- Page Header --}}
    <div style="margin-bottom:1.75rem;">
        <h1 style="font-size:1.6rem;font-weight:800;color:#0f172a;letter-spacing:-0.03em;margin:0;">Dashboard</h1>
        <p style="font-size:0.82rem;color:#94a3b8;margin-top:0.3rem;">Welcome back! Here's what's happening today.</p>
    </div>

    {{-- KPI Cards --}}
    <div class="kpi-grid" style="display:grid;grid-template-columns:repeat(4,1fr);gap:1rem;margin-bottom:1.75rem;">

        {{-- Total Leads --}}
        <div class="kpi-card" style="background:linear-gradient(135deg,#ffffff 0%,#f8f7ff 100%);border-radius:16px;border:1px solid #e8eaed;padding:1.3rem 1.4rem;position:relative;overflow:hidden;transition:all .25s cubic-bezier(.4,0,.2,1);">
            <div style="position:absolute;top:-15px;right:-15px;width:70px;height:70px;border-radius:50%;background:rgba(108,92,231,0.06);"></div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#6c5ce7,#a78bfa);display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                </div>
                <span style="font-size:0.72rem;font-weight:500;color:#94a3b8;text-transform:uppercase;letter-spacing:0.04em;">Total Leads</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:0.5rem;">
                <h3 style="font-size:2rem;font-weight:800;color:#0f172a;margin:0;letter-spacing:-0.02em;">{{ number_format($totalLeads) }}</h3>
                <span style="display:inline-flex;align-items:center;gap:0.2rem;font-size:0.68rem;font-weight:600;color:#10b981;background:#ecfdf5;padding:0.15rem 0.45rem;border-radius:999px;">
                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="23 6 13.5 15.5 8.5 10.5 1 18"/></svg>
                    +12.5%
                </span>
            </div>
            {{-- Mini sparkline --}}
            <div style="margin-top:0.8rem;height:28px;">
                <svg viewBox="0 0 120 28" preserveAspectRatio="none" style="width:100%;height:100%;">
                    <defs><linearGradient id="g1" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#6c5ce7" stop-opacity="0.15"/><stop offset="100%" stop-color="#6c5ce7" stop-opacity="0"/></linearGradient></defs>
                    <path d="M0,22 L15,18 L30,20 L45,14 L60,16 L75,10 L90,12 L105,6 L120,8 L120,28 L0,28Z" fill="url(#g1)"/>
                    <polyline points="0,22 15,18 30,20 45,14 60,16 75,10 90,12 105,6 120,8" fill="none" stroke="#6c5ce7" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        {{-- Revenue --}}
        <div class="kpi-card" style="background:linear-gradient(135deg,#ffffff 0%,#f0fdf4 100%);border-radius:16px;border:1px solid #e8eaed;padding:1.3rem 1.4rem;position:relative;overflow:hidden;transition:all .25s cubic-bezier(.4,0,.2,1);">
            <div style="position:absolute;top:-15px;right:-15px;width:70px;height:70px;border-radius:50%;background:rgba(16,185,129,0.06);"></div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#059669,#34d399);display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                </div>
                <span style="font-size:0.72rem;font-weight:500;color:#94a3b8;text-transform:uppercase;letter-spacing:0.04em;">Revenue (Est.)</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:0.5rem;">
                <h3 style="font-size:2rem;font-weight:800;color:#0f172a;margin:0;letter-spacing:-0.02em;">৳{{ number_format($revenue) }}</h3>
                <span style="display:inline-flex;align-items:center;gap:0.2rem;font-size:0.68rem;font-weight:600;color:#10b981;background:#ecfdf5;padding:0.15rem 0.45rem;border-radius:999px;">+5.2%</span>
            </div>
            <div style="margin-top:0.8rem;height:28px;">
                <svg viewBox="0 0 120 28" preserveAspectRatio="none" style="width:100%;height:100%;">
                    <defs><linearGradient id="g2" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#059669" stop-opacity="0.15"/><stop offset="100%" stop-color="#059669" stop-opacity="0"/></linearGradient></defs>
                    <path d="M0,24 L20,20 L40,22 L60,16 L80,12 L100,14 L120,6 L120,28 L0,28Z" fill="url(#g2)"/>
                    <polyline points="0,24 20,20 40,22 60,16 80,12 100,14 120,6" fill="none" stroke="#059669" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        {{-- Active Clients --}}
        <div class="kpi-card" style="background:linear-gradient(135deg,#ffffff 0%,#fff7ed 100%);border-radius:16px;border:1px solid #e8eaed;padding:1.3rem 1.4rem;position:relative;overflow:hidden;transition:all .25s cubic-bezier(.4,0,.2,1);">
            <div style="position:absolute;top:-15px;right:-15px;width:70px;height:70px;border-radius:50%;background:rgba(249,115,22,0.06);"></div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#ea580c,#fb923c);display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M3 21h18"/><path d="M3 7v1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7m0 1a3 3 0 0 0 6 0V7H3l2-4h14l2 4"/><path d="M5 21V10.9"/><path d="M19 21V10.9"/></svg>
                </div>
                <span style="font-size:0.72rem;font-weight:500;color:#94a3b8;text-transform:uppercase;letter-spacing:0.04em;">Active Clients</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:0.5rem;">
                <h3 style="font-size:2rem;font-weight:800;color:#0f172a;margin:0;letter-spacing:-0.02em;">{{ $totalClients }}</h3>
                <span style="display:inline-flex;align-items:center;gap:0.2rem;font-size:0.68rem;font-weight:600;color:#f97316;background:#fff7ed;padding:0.15rem 0.45rem;border-radius:999px;">{{ $pendingDemo }} pending</span>
            </div>
            <div style="margin-top:0.8rem;height:28px;">
                <svg viewBox="0 0 120 28" preserveAspectRatio="none" style="width:100%;height:100%;">
                    <defs><linearGradient id="g3" x1="0" y1="0" x2="0" y2="1"><stop offset="0%" stop-color="#ea580c" stop-opacity="0.12"/><stop offset="100%" stop-color="#ea580c" stop-opacity="0"/></linearGradient></defs>
                    <path d="M0,20 L20,18 L40,22 L60,14 L80,18 L100,10 L120,14 L120,28 L0,28Z" fill="url(#g3)"/>
                    <polyline points="0,20 20,18 40,22 60,14 80,18 100,10 120,14" fill="none" stroke="#ea580c" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </div>
        </div>

        {{-- Conversion Rate --}}
        <div class="kpi-card" style="background:linear-gradient(135deg,#ffffff 0%,#eef2ff 100%);border-radius:16px;border:1px solid #e8eaed;padding:1.3rem 1.4rem;position:relative;overflow:hidden;transition:all .25s cubic-bezier(.4,0,.2,1);">
            <div style="position:absolute;top:-15px;right:-15px;width:70px;height:70px;border-radius:50%;background:rgba(99,102,241,0.06);"></div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-bottom:0.75rem;">
                <div style="width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#4f46e5,#818cf8);display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                </div>
                <span style="font-size:0.72rem;font-weight:500;color:#94a3b8;text-transform:uppercase;letter-spacing:0.04em;">Conversion</span>
            </div>
            <div style="display:flex;align-items:baseline;gap:0.5rem;">
                <h3 style="font-size:2rem;font-weight:800;color:#0f172a;margin:0;letter-spacing:-0.02em;">{{ $convRate }}%</h3>
                <span style="display:inline-flex;align-items:center;gap:0.2rem;font-size:0.68rem;font-weight:600;color:#10b981;background:#ecfdf5;padding:0.15rem 0.45rem;border-radius:999px;">+1.5%</span>
            </div>
            {{-- Progress ring --}}
            <div style="margin-top:0.8rem;display:flex;align-items:center;gap:0.75rem;">
                <svg width="28" height="28" viewBox="0 0 36 36">
                    <circle cx="18" cy="18" r="15" fill="none" stroke="#e5e7eb" stroke-width="3"/>
                    <circle cx="18" cy="18" r="15" fill="none" stroke="#6c5ce7" stroke-width="3" stroke-dasharray="{{ $convRate * 0.94 }} 94" stroke-dashoffset="0" transform="rotate(-90 18 18)" stroke-linecap="round"/>
                </svg>
                <span style="font-size:0.68rem;color:#94a3b8;">of target 50%</span>
            </div>
        </div>
    </div>

    {{-- Secondary Grid: Leads + Tasks + Activity --}}
    <div style="display:grid;grid-template-columns:1fr 380px;gap:1.25rem;">

        {{-- Recent Leads Table --}}
        <div style="background:#fff;border-radius:16px;border:1px solid #e8eaed;overflow:hidden;">
            <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.4rem;border-bottom:1px solid #f1f3f5;">
                <div>
                    <h2 style="font-size:0.92rem;font-weight:700;color:#0f172a;margin:0;">Recent Leads</h2>
                    <p style="font-size:0.68rem;color:#94a3b8;margin:0.15rem 0 0;">Latest contacts added to CRM</p>
                </div>
                <a href="{{ route('filament.admin.resources.leads.index') }}" style="font-size:0.72rem;font-weight:600;color:#6c5ce7;text-decoration:none;background:rgba(108,92,231,0.06);padding:0.35rem 0.8rem;border-radius:6px;transition:all .15s;">View all →</a>
            </div>
            <table style="width:100%;border-collapse:collapse;">
                <thead>
                    <tr style="background:#fafafb;">
                        <th style="padding:0.6rem 1.4rem;text-align:left;font-size:0.62rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8;">Contact</th>
                        <th style="padding:0.6rem 1rem;text-align:left;font-size:0.62rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8;">Status</th>
                        <th style="padding:0.6rem 1rem;text-align:left;font-size:0.62rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8;">Source</th>
                        <th style="padding:0.6rem 1.4rem;text-align:right;font-size:0.62rem;font-weight:600;text-transform:uppercase;letter-spacing:0.06em;color:#94a3b8;">Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentLeads as $lead)
                    <tr style="border-top:1px solid #f3f4f6;transition:background .12s;" onmouseover="this.style.background='#f8f7ff'" onmouseout="this.style.background='transparent'">
                        <td style="padding:0.7rem 1.4rem;">
                            <div style="display:flex;align-items:center;gap:0.65rem;">
                                <div style="width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:0.6rem;font-weight:700;flex-shrink:0;background:linear-gradient(135deg,#6c5ce7,#a78bfa);color:#fff;">{{ $initials($lead->name) }}</div>
                                <div>
                                    <p style="font-size:0.78rem;font-weight:600;color:#0f172a;margin:0;">{{ $lead->name }}</p>
                                    <p style="font-size:0.68rem;color:#94a3b8;margin:0;">{{ $lead->email ?? '—' }}</p>
                                </div>
                            </div>
                        </td>
                        <td style="padding:0.7rem 1rem;">
                            @php $sc = match($lead->status) {
                                'won','qualified' => ['#ecfdf5','#059669','#a7f3d0'],
                                'contacted' => ['#eef2ff','#4338ca','#c7d2fe'],
                                'lost' => ['#fff1f2','#e11d48','#fecdd3'],
                                default => ['#f8fafc','#64748b','#e2e8f0'],
                            }; @endphp
                            <span style="padding:0.18rem 0.6rem;border-radius:999px;font-size:0.6rem;font-weight:600;background:{{ $sc[0] }};color:{{ $sc[1] }};border:1px solid {{ $sc[2] }};">{{ strtoupper($lead->status) }}</span>
                        </td>
                        <td style="padding:0.7rem 1rem;font-size:0.78rem;color:#64748b;">{{ ucfirst($lead->source) }}</td>
                        <td style="padding:0.7rem 1.4rem;font-size:0.72rem;color:#94a3b8;text-align:right;">{{ $lead->created_at->format('M d') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" style="padding:3rem;text-align:center;">
                            <div style="color:#94a3b8;">
                                <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin:0 auto 0.5rem;display:block;opacity:0.3;"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" y1="8" x2="19" y2="14"/><line x1="22" y1="11" x2="16" y2="11"/></svg>
                                <p style="font-size:0.82rem;font-weight:500;margin:0;">No leads yet</p>
                                <a href="{{ route('filament.admin.resources.leads.create') }}" style="font-size:0.72rem;font-weight:600;color:#6c5ce7;text-decoration:none;">Add your first lead →</a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Right Column: Tasks + Quick Stats --}}
        <div style="display:flex;flex-direction:column;gap:1.25rem;">

            {{-- Task Progress Card --}}
            <div style="background:linear-gradient(135deg,#6c5ce7 0%,#5a4bd1 100%);border-radius:16px;padding:1.3rem 1.4rem;color:#fff;">
                <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:0.8rem;">
                    <span style="font-size:0.72rem;font-weight:500;opacity:0.8;text-transform:uppercase;letter-spacing:0.04em;">Task Progress</span>
                    <span style="font-size:1.2rem;font-weight:800;">{{ $taskProgress }}%</span>
                </div>
                <div style="background:rgba(255,255,255,0.15);border-radius:999px;height:6px;overflow:hidden;">
                    <div style="background:#fff;height:100%;border-radius:999px;width:{{ $taskProgress }}%;transition:width 1s cubic-bezier(.4,0,.2,1);"></div>
                </div>
                <div style="display:flex;justify-content:space-between;margin-top:0.6rem;font-size:0.68rem;opacity:0.75;">
                    <span>{{ $completedTasks }} completed</span>
                    <span>{{ $totalTasks - $completedTasks }} remaining</span>
                </div>
            </div>

            {{-- Upcoming Tasks --}}
            <div style="background:#fff;border-radius:16px;border:1px solid #e8eaed;flex:1;display:flex;flex-direction:column;">
                <div style="display:flex;align-items:center;justify-content:space-between;padding:1.1rem 1.4rem;border-bottom:1px solid #f1f3f5;">
                    <h2 style="font-size:0.92rem;font-weight:700;color:#0f172a;margin:0;">Upcoming Tasks</h2>
                    <a href="{{ route('filament.admin.resources.tasks.create') }}" style="width:28px;height:28px;background:#6c5ce7;border-radius:8px;display:flex;align-items:center;justify-content:center;color:#fff;text-decoration:none;font-size:1.1rem;line-height:1;transition:all .15s;" onmouseover="this.style.background='#5a4bd1'" onmouseout="this.style.background='#6c5ce7'">+</a>
                </div>
                <div style="padding:0.5rem;flex:1;">
                    @forelse($upcomingTasks as $task)
                    <div style="display:flex;align-items:flex-start;gap:0.7rem;padding:0.6rem 0.75rem;border-radius:8px;transition:background .12s;" onmouseover="this.style.background='#f8f7ff'" onmouseout="this.style.background='transparent'">
                        <div style="margin-top:2px;flex-shrink:0;">
                            <div style="width:16px;height:16px;border-radius:4px;border:2px solid #d1d5db;"></div>
                        </div>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:0.78rem;font-weight:500;color:#0f172a;margin:0;">{{ Str::limit($task->title, 32) }}</p>
                            <div style="display:flex;align-items:center;gap:0.4rem;margin-top:0.2rem;">
                                @if($task->due_date)
                                <span style="font-size:0.62rem;color:#94a3b8;">📅 {{ \Carbon\Carbon::parse($task->due_date)->format('M d') }}</span>
                                @endif
                                @if(isset($task->priority))
                                <span style="font-size:0.58rem;font-weight:600;text-transform:uppercase;padding:0.1rem 0.35rem;border-radius:4px;background:{{ $task->priority === 'high' || $task->priority === 'urgent' ? '#fff1f2' : '#eef2ff' }};color:{{ $task->priority === 'high' || $task->priority === 'urgent' ? '#e11d48' : '#4338ca' }};">{{ $task->priority }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    @empty
                    <div style="text-align:center;padding:2rem;color:#94a3b8;font-size:0.78rem;">
                        <p style="margin:0;">No pending tasks 🎉</p>
                    </div>
                    @endforelse
                </div>
                <a href="{{ route('filament.admin.resources.tasks.index') }}" style="display:block;text-align:center;padding:0.75rem;font-size:0.72rem;font-weight:600;color:#64748b;text-decoration:none;border-top:1px solid #f1f3f5;transition:color .15s;" onmouseover="this.style.color='#6c5ce7'" onmouseout="this.style.color='#64748b'">View all tasks →</a>
            </div>
        </div>
    </div>
</div>

<style>
.kpi-card:hover {
    transform: translateY(-3px) !important;
    box-shadow: 0 8px 25px rgba(0,0,0,0.08) !important;
}
@media (max-width: 1200px) {
    .kpi-grid { grid-template-columns: repeat(2,1fr) !important; }
}
@media (max-width: 900px) {
    div[style*="1fr 380px"] { grid-template-columns: 1fr !important; }
}
@media (max-width: 640px) {
    .kpi-grid { grid-template-columns: 1fr !important; }
}
</style>
