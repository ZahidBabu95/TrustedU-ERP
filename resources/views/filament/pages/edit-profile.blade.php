<x-filament-panels::page>
@php
    $user = auth()->user();
    $profile = $user->profile;
    $teams = $user->teams;
    $docs = $user->documents;
    $joinDate = $profile?->joining_date ?? $user->created_at;
    $daysSinceJoining = $joinDate ? (int) floor($joinDate->diffInDays(now())) : 0;
@endphp

{{-- ─── Hero Cover Section ─── --}}
<div style="position:relative;overflow:hidden;border-radius:20px;background:linear-gradient(135deg,#312e81 0%,#4f46e5 30%,#6366f1 60%,#818cf8 100%);margin-bottom:24px;box-shadow:0 4px 20px rgba(99,102,241,.25);">
    {{-- Decorative Elements --}}
    <div style="position:absolute;inset:0;overflow:hidden;pointer-events:none;">
        <div style="position:absolute;top:-40px;right:-40px;width:200px;height:200px;background:rgba(255,255,255,.06);border-radius:50%;"></div>
        <div style="position:absolute;bottom:-60px;left:20%;width:300px;height:300px;background:rgba(255,255,255,.04);border-radius:50%;"></div>
        <div style="position:absolute;top:30%;right:15%;width:80px;height:80px;background:rgba(255,255,255,.05);border-radius:50%;"></div>
    </div>

    <div style="position:relative;padding:28px 24px 24px;">
        {{-- Role Badges --}}
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:20px;flex-wrap:wrap;">
            <span style="display:inline-flex;align-items:center;gap:4px;padding:4px 10px;background:rgba(255,255,255,.12);border-radius:6px;font-size:11px;color:rgba(255,255,255,.85);backdrop-filter:blur(4px);">
                <svg style="width:12px;height:12px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.745 3.745 0 011.043 3.296A3.745 3.745 0 0121 12z" /></svg>
                {{ $user->is_active ? 'Active' : 'Inactive' }}
            </span>
            <span style="padding:4px 10px;background:rgba(255,255,255,.12);border-radius:6px;font-size:11px;color:rgba(255,255,255,.85);">
                {{ ucwords(str_replace('_', ' ', $user->role)) }}
            </span>
            @if($profile?->employment_type)
                <span style="padding:4px 10px;background:rgba(255,255,255,.08);border-radius:6px;font-size:11px;color:rgba(255,255,255,.7);">
                    {{ ucwords(str_replace('_', ' ', $profile->employment_type)) }}
                </span>
            @endif
        </div>

        {{-- Profile Row --}}
        <div style="display:flex;align-items:center;gap:18px;flex-wrap:wrap;">
            {{-- Avatar --}}
            @if($user->getFilamentAvatarUrl())
                <img src="{{ $user->getFilamentAvatarUrl() }}" alt="{{ $user->name }}"
                    style="width:80px;height:80px;border-radius:18px;object-fit:cover;border:3px solid rgba(255,255,255,.25);box-shadow:0 4px 12px rgba(0,0,0,.2);flex-shrink:0;">
            @else
                <div style="width:80px;height:80px;border-radius:18px;background:rgba(255,255,255,.15);border:3px solid rgba(255,255,255,.25);display:flex;align-items:center;justify-content:center;font-size:26px;font-weight:800;color:rgba(255,255,255,.9);backdrop-filter:blur(8px);flex-shrink:0;">
                    {{ $user->initials }}
                </div>
            @endif

            {{-- Info --}}
            <div style="flex:1;min-width:0;">
                <h1 style="font-size:22px;font-weight:800;color:#fff;margin:0 0 3px;line-height:1.2;">{{ $user->name }}</h1>
                <p style="font-size:13px;color:rgba(255,255,255,.7);margin:0 0 6px;">{{ $user->email }}</p>
                <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap;">
                    @if($user->designation)
                        <span style="display:flex;align-items:center;gap:3px;font-size:11px;color:rgba(255,255,255,.6);">
                            <svg style="width:12px;height:12px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 14.15v4.25c0 1.094-.787 2.036-1.872 2.18-2.087.277-4.216.42-6.378.42s-4.291-.143-6.378-.42c-1.085-.144-1.872-1.086-1.872-2.18v-4.25m16.5 0a2.18 2.18 0 00.75-1.661V8.706c0-1.081-.768-2.015-1.837-2.175a48.114 48.114 0 00-3.413-.387m4.5 8.006c-.194.165-.42.295-.673.38A23.978 23.978 0 0112 15.75c-2.648 0-5.195-.429-7.577-1.22a2.016 2.016 0 01-.673-.38m0 0A2.18 2.18 0 013 12.489V8.706c0-1.081.768-2.015 1.837-2.175a48.111 48.111 0 013.413-.387m7.5 0V5.25A2.25 2.25 0 0013.5 3h-3a2.25 2.25 0 00-2.25 2.25v.894m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                            {{ $user->designation }}
                        </span>
                    @endif
                    @if($user->department)
                        <span style="display:flex;align-items:center;gap:3px;font-size:11px;color:rgba(255,255,255,.6);">
                            <svg style="width:12px;height:12px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                            {{ $user->department }}
                        </span>
                    @endif
                    @if($user->phone)
                        <span style="display:flex;align-items:center;gap:3px;font-size:11px;color:rgba(255,255,255,.6);">
                            <svg style="width:12px;height:12px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" /></svg>
                            {{ $user->phone }}
                        </span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ─── Quick Stats ─── --}}
<div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:24px;">
    @php
        $stats = [
            ['label' => 'Teams', 'value' => $teams->count(), 'color' => '#6366f1', 'bg' => '#eef2ff', 'icon' => 'M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z'],
            ['label' => 'Documents', 'value' => $docs->count(), 'color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => 'M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z'],
            ['label' => 'Days Active', 'value' => $daysSinceJoining, 'color' => '#22c55e', 'bg' => '#f0fdf4', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Last Login', 'value' => $user->last_login_at ? $user->last_login_at->diffForHumans(short: true) : '—', 'color' => '#8b5cf6', 'bg' => '#f5f3ff', 'icon' => 'M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9'],
        ];
    @endphp
    @foreach($stats as $stat)
        <div style="background:#fff;border:1px solid #f1f5f9;border-radius:14px;padding:14px 16px;display:flex;align-items:center;gap:10px;box-shadow:0 1px 2px rgba(0,0,0,.03);transition:all .2s;" onmouseover="this.style.transform='translateY(-2px)';this.style.boxShadow='0 4px 12px rgba(0,0,0,.06)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 1px 2px rgba(0,0,0,.03)'">
            <span style="display:inline-flex;align-items:center;justify-content:center;width:36px;height:36px;border-radius:10px;background:{{ $stat['bg'] }};flex-shrink:0;">
                <svg style="width:18px;height:18px;color:{{ $stat['color'] }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" /></svg>
            </span>
            <div>
                <p style="font-size:18px;font-weight:700;color:#0f172a;margin:0;line-height:1;">{{ $stat['value'] }}</p>
                <p style="font-size:11px;color:#94a3b8;margin:1px 0 0;font-weight:500;">{{ $stat['label'] }}</p>
            </div>
        </div>
    @endforeach
</div>

{{-- ─── Teams & Documents Row ─── --}}
<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:24px;">
    {{-- Teams --}}
    <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;padding:16px 18px;box-shadow:0 1px 2px rgba(0,0,0,.03);">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px;">
            <svg style="width:15px;height:15px;color:#6366f1;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 003.741-.479 3 3 0 00-4.682-2.72m.94 3.198l.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0112 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 016 18.719m12 0a5.971 5.971 0 00-.941-3.197m0 0A5.995 5.995 0 0012 12.75a5.995 5.995 0 00-5.058 2.772m0 0a3 3 0 00-4.681 2.72 8.986 8.986 0 003.74.477m.94-3.197a5.971 5.971 0 00-.94 3.197M15 6.75a3 3 0 11-6 0 3 3 0 016 0zm6 3a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0zm-13.5 0a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /></svg>
            <h3 style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">My Teams</h3>
        </div>
        @if($teams->count() > 0)
            <div style="display:flex;flex-wrap:wrap;gap:6px;">
                @foreach($teams as $team)
                    <div style="display:inline-flex;align-items:center;gap:5px;padding:5px 10px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;font-size:12px;">
                        <span style="width:18px;height:18px;border-radius:4px;display:inline-flex;align-items:center;justify-content:center;font-size:9px;font-weight:700;color:#fff;background:{{ $team->color ?? '#6366f1' }};">{{ strtoupper(substr($team->name, 0, 1)) }}</span>
                        <span style="font-weight:500;color:#334155;">{{ $team->name }}</span>
                        @if($user->current_team_id === $team->id)
                            <span style="font-size:9px;font-weight:600;padding:1px 5px;background:#dcfce7;color:#16a34a;border-radius:4px;">Active</span>
                        @endif
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:12px;color:#94a3b8;margin:0;">Not assigned to any team</p>
        @endif
    </div>

    {{-- Documents --}}
    <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;padding:16px 18px;box-shadow:0 1px 2px rgba(0,0,0,.03);">
        <div style="display:flex;align-items:center;gap:6px;margin-bottom:10px;">
            <svg style="width:15px;height:15px;color:#f59e0b;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
            <h3 style="font-size:13px;font-weight:700;color:#0f172a;margin:0;">My Documents</h3>
        </div>
        @if($docs->count() > 0)
            <div style="display:flex;flex-direction:column;gap:6px;">
                @foreach($docs->take(5) as $doc)
                    <div style="display:flex;align-items:center;gap:8px;padding:6px 10px;background:#fafafa;border-radius:8px;">
                        <svg style="width:16px;height:16px;color:#d97706;flex-shrink:0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" /></svg>
                        <span style="font-size:12px;font-weight:500;color:#334155;flex:1;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $doc->title }}</span>
                        <span style="font-size:10px;color:#94a3b8;">{{ $doc->file_size_formatted }}</span>
                    </div>
                @endforeach
            </div>
        @else
            <p style="font-size:12px;color:#94a3b8;margin:0;">No documents uploaded</p>
        @endif
    </div>
</div>

{{-- ─── Edit Form ─── --}}
<form wire:submit="save">
    {{ $this->form }}

    <div style="margin-top:20px;display:flex;justify-content:flex-end;padding:8px 0;">
        <button type="submit"
            style="display:inline-flex;align-items:center;gap:6px;padding:10px 24px;font-size:14px;font-weight:600;color:#fff;background:linear-gradient(135deg,#4f46e5,#6366f1);border:none;border-radius:10px;cursor:pointer;box-shadow:0 2px 8px rgba(99,102,241,.3);transition:all .2s;"
            onmouseover="this.style.transform='translateY(-1px)';this.style.boxShadow='0 4px 16px rgba(99,102,241,.4)'" onmouseout="this.style.transform='translateY(0)';this.style.boxShadow='0 2px 8px rgba(99,102,241,.3)'">
            <svg style="width:18px;height:18px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
            Save Profile Changes
        </button>
    </div>
</form>
</x-filament-panels::page>

{{-- ─── Responsive Styles ─── --}}
<style>
    @media (max-width: 768px) {
        [style*="grid-template-columns:repeat(4"] {
            grid-template-columns: repeat(2, 1fr) !important;
        }
        [style*="grid-template-columns:1fr 1fr"] {
            grid-template-columns: 1fr !important;
        }
    }
    @media (max-width: 480px) {
        [style*="grid-template-columns:repeat(2"] {
            grid-template-columns: 1fr !important;
        }
    }
</style>
