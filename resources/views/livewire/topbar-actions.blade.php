<script>
    document.addEventListener('DOMContentLoaded', () => {
        // Nuke the weird outer frame and pill shapes
        document.querySelectorAll('div, main, section, html, body').forEach(el => {
            const rect = el.getBoundingClientRect();
            // If element takes up almost the whole screen
            if (rect.width > window.innerWidth * 0.9 && rect.height > window.innerHeight * 0.9) {
                el.style.setProperty('border-radius', '0px', 'important');
                el.style.setProperty('border', 'none', 'important');
                el.style.setProperty('box-shadow', 'none', 'important');
                el.style.setProperty('margin', '0px', 'important');
                el.style.setProperty('padding', '0px', 'important');
                // Also kill the weird "mac window" grey background if it's a layout parent
                if (el.tagName !== 'HTML' && el.tagName !== 'BODY') {
                    el.style.setProperty('background', 'transparent', 'important');
                }
            }
        });
        
        // Find specifically the right-aligned topbar pill and kill its border
        document.querySelectorAll('.fi-topbar-end, .fi-topbar-end > div').forEach(el => {
            el.style.setProperty('border', 'none', 'important');
            el.style.setProperty('box-shadow', 'none', 'important');
            el.style.setProperty('background', 'transparent', 'important');
        });
    });
</script>
<div style="display:flex;align-items:center;gap:8px;margin-left:8px;">

    {{-- ─── Team Switcher ─── --}}
    <div x-data="{ open: false }" style="position:relative;">
        <button @click="open = !open" type="button"
            style="display:flex;align-items:center;gap:6px;padding:5px 10px;font-size:13px;font-weight:500;border-radius:8px;border:1px solid #e5e7eb;background:#fff;cursor:pointer;white-space:nowrap;transition:all .15s ease;"
            onmouseover="this.style.background='#f9fafb';this.style.borderColor='#d1d5db'" onmouseout="this.style.background='#fff';this.style.borderColor='#e5e7eb'"
        >
            @if($currentTeam)
                <span style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:4px;font-size:10px;font-weight:700;color:#fff;background:{{ $currentTeam->color ?? '#6366f1' }};flex-shrink:0;">
                    {{ strtoupper(substr($currentTeam->name, 0, 1)) }}
                </span>
                <span style="max-width:90px;overflow:hidden;text-overflow:ellipsis;color:#374151;">{{ $currentTeam->name }}</span>
            @else
                <svg style="width:16px;height:16px;color:#9ca3af;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m10.5-18v18m6-13.5V21M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008z" /></svg>
                <span style="color:#6b7280;">{{ $isAdmin ? 'All' : 'Team' }}</span>
            @endif
            <svg width="12" height="12" style="width:12px !important;height:12px !important;min-width:12px;max-width:12px;min-height:12px;max-height:12px;color:#9ca3af;transition:transform .15s;flex-shrink:0;" :style="open ? 'transform:rotate(180deg)' : ''" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
        </button>

        <div x-show="open" @click.away="open = false" x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-75"
             style="position:absolute;right:0;z-index:9999;margin-top:6px;width:220px;background:#fff;border-radius:12px;box-shadow:0 10px 25px -5px rgba(0,0,0,.1),0 4px 10px -6px rgba(0,0,0,.1);border:1px solid #e5e7eb;padding:4px;overflow:hidden;">

            <div style="padding:6px 10px;font-size:10px;font-weight:600;color:#9ca3af;text-transform:uppercase;letter-spacing:.06em;">Switch Team</div>

            @if($isAdmin)
                <button wire:click="clearTeam" @click="open = false"
                    style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;font-size:13px;border-radius:8px;border:none;cursor:pointer;transition:background .1s;
                    {{ !$selectedTeamId ? 'background:#eef2ff;color:#4338ca;font-weight:600;' : 'background:transparent;color:#4b5563;' }}"
                    onmouseover="this.style.background='{{ !$selectedTeamId ? '#eef2ff' : '#f9fafb' }}'" onmouseout="this.style.background='{{ !$selectedTeamId ? '#eef2ff' : 'transparent' }}'">
                    <svg style="width:16px;height:16px;flex-shrink:0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 017.843 4.582M12 3a8.997 8.997 0 00-7.843 4.582m15.686 0A11.953 11.953 0 0112 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0121 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0112 16.5a17.88 17.88 0 01-8.716-2.247m0 0A9.015 9.015 0 013 12c0-1.605.42-3.113 1.157-4.418" /></svg>
                    <span>All Teams</span>
                    @if(!$selectedTeamId)
                        <svg style="width:14px;height:14px;margin-left:auto;color:#4f46e5;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                    @endif
                </button>
                <div style="margin:2px 0;border-top:1px solid #f3f4f6;"></div>
            @endif

            @forelse($teams as $team)
                <button wire:click="switchTeam({{ $team->id }})" @click="open = false"
                    style="width:100%;display:flex;align-items:center;gap:8px;padding:7px 10px;font-size:13px;border-radius:8px;border:none;cursor:pointer;transition:background .1s;
                    {{ $selectedTeamId == $team->id ? 'background:#eef2ff;color:#4338ca;font-weight:600;' : 'background:transparent;color:#4b5563;' }}"
                    onmouseover="this.style.background='{{ $selectedTeamId == $team->id ? '#eef2ff' : '#f9fafb' }}'" onmouseout="this.style.background='{{ $selectedTeamId == $team->id ? '#eef2ff' : 'transparent' }}'">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:20px;height:20px;border-radius:4px;font-size:10px;font-weight:700;color:#fff;flex-shrink:0;background:{{ $team->color ?? '#6366f1' }}">
                        {{ strtoupper(substr($team->name, 0, 1)) }}
                    </span>
                    <span style="overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $team->name }}</span>
                    @if($selectedTeamId == $team->id)
                        <svg style="width:14px;height:14px;margin-left:auto;color:#4f46e5;flex-shrink:0;" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" /></svg>
                    @endif
                </button>
            @empty
                <div style="padding:12px;text-align:center;font-size:13px;color:#9ca3af;">No teams</div>
            @endforelse
        </div>
    </div>

    {{-- ─── Notification Bell ─── --}}
    <div x-data="{ open: false }" style="position:relative;">
        <button @click="open = !open" type="button"
            style="position:relative;display:flex;align-items:center;justify-content:center;width:34px;height:34px;border-radius:8px;border:none;background:transparent;cursor:pointer;color:#6b7280;transition:all .15s;"
            onmouseover="this.style.background='#f3f4f6';this.style.color='#374151'" onmouseout="this.style.background='transparent';this.style.color='#6b7280'">
            <svg style="width:20px;height:20px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
            @if($unreadCount > 0)
                <span style="position:absolute;top:-2px;right:-2px;display:flex;align-items:center;justify-content:center;min-width:16px;height:16px;padding:0 4px;font-size:10px;font-weight:700;color:#fff;background:#ef4444;border-radius:999px;box-shadow:0 0 0 2px #fff;">
                    {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                </span>
            @endif
        </button>

        <div x-show="open" @click.away="open = false" x-cloak
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-75"
             style="position:absolute;right:0;z-index:9999;margin-top:6px;width:340px;background:#fff;border-radius:12px;box-shadow:0 10px 25px -5px rgba(0,0,0,.15),0 4px 10px -6px rgba(0,0,0,.1);border:1px solid #e5e7eb;overflow:hidden;">

            {{-- Header --}}
            <div style="display:flex;align-items:center;justify-content:space-between;padding:12px 14px;border-bottom:1px solid #f3f4f6;">
                <div style="display:flex;align-items:center;gap:6px;">
                    <span style="font-size:14px;font-weight:600;color:#111827;">Notifications</span>
                    @if($unreadCount > 0)
                        <span style="display:inline-flex;align-items:center;justify-content:center;min-width:18px;height:18px;padding:0 5px;font-size:10px;font-weight:700;color:#fff;background:#ef4444;border-radius:999px;">{{ $unreadCount }}</span>
                    @endif
                </div>
                <div style="display:flex;align-items:center;gap:4px;">
                    @if($unreadCount > 0)
                        <button wire:click="markAllRead" style="font-size:11px;color:#4f46e5;font-weight:500;padding:3px 8px;border-radius:6px;border:none;background:transparent;cursor:pointer;" onmouseover="this.style.background='#eef2ff'" onmouseout="this.style.background='transparent'">
                            Mark all read
                        </button>
                    @endif
                    <a href="{{ url('/admin/notifications') }}" style="font-size:11px;color:#6b7280;padding:3px 8px;border-radius:6px;text-decoration:none;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                        View all
                    </a>
                </div>
            </div>

            {{-- List --}}
            <div style="max-height:320px;overflow-y:auto;">
                @forelse($notifications as $notification)
                    <div style="display:flex;align-items:flex-start;gap:10px;padding:10px 14px;border-bottom:1px solid #fafafa;{{ $notification->isRead() ? '' : 'background:#fafaff;' }}">
                        @php
                            $colors = ['primary'=>['#eef2ff','#4f46e5'],'success'=>['#ecfdf5','#059669'],'warning'=>['#fffbeb','#d97706'],'danger'=>['#fef2f2','#dc2626'],'info'=>['#eff6ff','#2563eb']];
                            $c = $colors[$notification->color] ?? $colors['primary'];
                        @endphp
                        <span style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:32px;height:32px;border-radius:8px;background:{{ $c[0] }};color:{{ $c[1] }};">
                            <svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        </span>
                        <div style="flex:1;min-width:0;">
                            <p style="font-size:13px;font-weight:600;color:#1f2937;margin:0;line-height:1.3;">{{ $notification->title }}</p>
                            @if($notification->message)
                                <p style="font-size:12px;color:#6b7280;margin:2px 0 0;line-height:1.3;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $notification->message }}</p>
                            @endif
                            <p style="font-size:10px;color:#9ca3af;margin:3px 0 0;">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                        <div style="display:flex;align-items:center;gap:2px;flex-shrink:0;">
                            @if(!$notification->isRead())
                                <button wire:click="markAsRead('{{ $notification->id }}')" style="padding:4px;border-radius:4px;border:none;background:transparent;cursor:pointer;color:#9ca3af;" onmouseover="this.style.color='#059669';this.style.background='#ecfdf5'" onmouseout="this.style.color='#9ca3af';this.style.background='transparent'" title="Mark read">
                                    <svg style="width:14px;height:14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                </button>
                            @endif
                            <button wire:click="deleteNotification('{{ $notification->id }}')" style="padding:4px;border-radius:4px;border:none;background:transparent;cursor:pointer;color:#9ca3af;" onmouseover="this.style.color='#dc2626';this.style.background='#fef2f2'" onmouseout="this.style.color='#9ca3af';this.style.background='transparent'" title="Delete">
                                <svg style="width:14px;height:14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                    </div>
                @empty
                    <div style="padding:30px 0;text-align:center;">
                        <svg style="width:36px;height:36px;margin:0 auto 8px;color:#e5e7eb;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        <p style="font-size:13px;color:#9ca3af;margin:0;">No notifications</p>
                    </div>
                @endforelse
            </div>

            @if($notifications->count() > 0)
                <div style="padding:8px 14px;border-top:1px solid #f3f4f6;text-align:center;">
                    <a href="{{ url('/admin/notifications') }}" style="font-size:12px;font-weight:500;color:#4f46e5;text-decoration:none;" onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'">
                        View all notifications →
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
