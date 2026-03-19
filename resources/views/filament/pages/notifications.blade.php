<x-filament-panels::page>
<div x-data="{
    soundEnabled: localStorage.getItem('trustedu_notification_sound') !== 'false',
    pushEnabled: localStorage.getItem('trustedu_notification_push') !== 'false',
    pushPermission: typeof Notification !== 'undefined' ? Notification.permission : 'denied',
    toggleSound() {
        this.soundEnabled = !this.soundEnabled;
        localStorage.setItem('trustedu_notification_sound', this.soundEnabled ? 'true' : 'false');
        if (this.soundEnabled && window.TrustedUNotifications) window.TrustedUNotifications.playSound();
    },
    togglePush() {
        this.pushEnabled = !this.pushEnabled;
        localStorage.setItem('trustedu_notification_push', this.pushEnabled ? 'true' : 'false');
        if (this.pushEnabled && typeof Notification !== 'undefined' && Notification.permission === 'default') {
            Notification.requestPermission().then(p => this.pushPermission = p);
        }
    },
    requestPush() {
        if (typeof Notification !== 'undefined') {
            Notification.requestPermission().then(p => this.pushPermission = p);
        }
    },
    testSound() { if (window.TrustedUNotifications) window.TrustedUNotifications.playSound(); },
    testPush() { if (window.TrustedUNotifications) window.TrustedUNotifications.testNotification(); },
}">

    {{-- ─── Stats Cards ─── --}}
    <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;">
        @foreach([
            ['label' => 'Total', 'value' => $this->stats['total'], 'color' => '#6366f1', 'bg' => '#eef2ff', 'icon' => 'M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0'],
            ['label' => 'Unread', 'value' => $this->stats['unread'], 'color' => '#ef4444', 'bg' => '#fef2f2', 'icon' => 'M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75'],
            ['label' => 'Read', 'value' => $this->stats['read'], 'color' => '#22c55e', 'bg' => '#f0fdf4', 'icon' => 'M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Today', 'value' => $this->stats['today'], 'color' => '#f59e0b', 'bg' => '#fffbeb', 'icon' => 'M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z'],
        ] as $stat)
            <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;padding:20px;display:flex;align-items:center;gap:14px;box-shadow:0 1px 3px rgba(0,0,0,.04);transition:all .2s;" onmouseover="this.style.boxShadow='0 4px 12px rgba(0,0,0,.08)';this.style.transform='translateY(-2px)'" onmouseout="this.style.boxShadow='0 1px 3px rgba(0,0,0,.04)';this.style.transform='translateY(0)'">
                <span style="display:inline-flex;align-items:center;justify-content:center;width:44px;height:44px;border-radius:12px;background:{{ $stat['bg'] }};">
                    <svg style="width:22px;height:22px;color:{{ $stat['color'] }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="{{ $stat['icon'] }}" /></svg>
                </span>
                <div>
                    <p style="font-size:24px;font-weight:800;color:#0f172a;margin:0;line-height:1;">{{ $stat['value'] }}</p>
                    <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;font-weight:500;">{{ $stat['label'] }}</p>
                </div>
            </div>
        @endforeach
    </div>

    {{-- ─── Tab Navigation + Actions ─── --}}
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;flex-wrap:wrap;gap:12px;">
        {{-- Tabs --}}
        <div style="display:flex;align-items:center;gap:2px;background:#f1f5f9;border-radius:10px;padding:3px;">
            @foreach(['all' => 'All', 'unread' => 'Unread', 'read' => 'Read', 'settings' => '⚙ Settings'] as $key => $label)
                <button wire:click="setTab('{{ $key }}')"
                    style="padding:7px 16px;font-size:13px;font-weight:{{ $activeTab === $key ? '600' : '400' }};border-radius:8px;border:none;cursor:pointer;transition:all .15s;
                    {{ $activeTab === $key ? 'background:#fff;color:#0f172a;box-shadow:0 1px 3px rgba(0,0,0,.1);' : 'background:transparent;color:#64748b;' }}"
                    onmouseover="if('{{ $activeTab }}' !== '{{ $key }}')this.style.color='#374151'" onmouseout="if('{{ $activeTab }}' !== '{{ $key }}')this.style.color='#64748b'">
                    {{ $label }}
                    @if($key === 'unread' && $this->stats['unread'] > 0)
                        <span style="margin-left:4px;display:inline-flex;align-items:center;justify-content:center;min-width:16px;height:16px;padding:0 4px;font-size:10px;font-weight:700;color:#fff;background:#ef4444;border-radius:999px;">{{ $this->stats['unread'] }}</span>
                    @endif
                </button>
            @endforeach
        </div>

        {{-- Action Buttons --}}
        @if($activeTab !== 'settings')
        <div style="display:flex;align-items:center;gap:6px;">
            {{-- Search --}}
            <div style="position:relative;">
                <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search notifications..."
                    style="padding:7px 12px 7px 32px;font-size:13px;border:1px solid #e2e8f0;border-radius:8px;width:220px;outline:none;transition:border .15s;"
                    onfocus="this.style.borderColor='#6366f1';this.style.boxShadow='0 0 0 3px rgba(99,102,241,.1)'" onblur="this.style.borderColor='#e2e8f0';this.style.boxShadow='none'">
                <svg style="position:absolute;left:10px;top:50%;transform:translateY(-50%);width:15px;height:15px;color:#94a3b8;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" /></svg>
            </div>

            {{-- Type Filter --}}
            <select wire:model.live="typeFilter"
                style="padding:7px 10px;font-size:13px;border:1px solid #e2e8f0;border-radius:8px;background:#fff;outline:none;cursor:pointer;">
                <option value="">All Types</option>
                @foreach($this->types as $type => $count)
                    <option value="{{ $type }}">{{ ucwords(str_replace('_', ' ', $type)) }} ({{ $count }})</option>
                @endforeach
            </select>

            @if($this->stats['unread'] > 0)
                <button wire:click="markAllRead"
                    style="display:inline-flex;align-items:center;gap:4px;padding:7px 14px;font-size:12px;font-weight:600;color:#4f46e5;background:#eef2ff;border:none;border-radius:8px;cursor:pointer;transition:all .15s;"
                    onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                    <svg style="width:14px;height:14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                    Mark all read
                </button>
            @endif

            <button wire:click="deleteAllRead" wire:confirm="Delete all read notifications?"
                style="display:inline-flex;align-items:center;gap:4px;padding:7px 14px;font-size:12px;font-weight:600;color:#dc2626;background:#fef2f2;border:none;border-radius:8px;cursor:pointer;transition:all .15s;"
                onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                <svg style="width:14px;height:14px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                Clear read
            </button>
        </div>
        @endif
    </div>

    {{-- ─── Settings Tab ─── --}}
    @if($activeTab === 'settings')
        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(380px,1fr));gap:20px;">

            {{-- Sound Settings --}}
            <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#fef3c7;">
                        <svg style="width:20px;height:20px;color:#f59e0b;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 010 12.728M16.463 8.288a5.25 5.25 0 010 7.424M6.75 8.25l4.72-4.72a.75.75 0 011.28.53v15.88a.75.75 0 01-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.01 9.01 0 012.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75z" /></svg>
                    </span>
                    <div>
                        <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Notification Sound</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">WhatsApp-style notification tone</p>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:#f8fafc;border-radius:10px;margin-bottom:12px;">
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#334155;margin:0;">Enable Sound</p>
                        <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">Play sound when new notification arrives</p>
                    </div>
                    <button @click="toggleSound()" style="position:relative;width:44px;height:24px;border-radius:12px;border:none;cursor:pointer;transition:background .2s;padding:0;"
                        :style="soundEnabled ? 'background:#6366f1' : 'background:#cbd5e1'">
                        <span style="position:absolute;top:2px;width:20px;height:20px;background:#fff;border-radius:50%;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:left .2s;"
                            :style="soundEnabled ? 'left:22px' : 'left:2px'"></span>
                    </button>
                </div>

                <button @click="testSound()"
                    style="width:100%;padding:10px;font-size:13px;font-weight:600;color:#6366f1;background:#eef2ff;border:1px solid #e0e7ff;border-radius:10px;cursor:pointer;transition:all .15s;"
                    onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                    🔔 Test Notification Sound
                </button>
            </div>

            {{-- Push Notification Settings --}}
            <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#dbeafe;">
                        <svg style="width:20px;height:20px;color:#3b82f6;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 1.5H8.25A2.25 2.25 0 006 3.75v16.5a2.25 2.25 0 002.25 2.25h7.5A2.25 2.25 0 0018 20.25V3.75a2.25 2.25 0 00-2.25-2.25H13.5m-3 0V3h3V1.5m-3 0h3m-3 18.75h3" /></svg>
                    </span>
                    <div>
                        <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Push Notifications</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">Browser desktop notifications</p>
                    </div>
                </div>

                <div style="display:flex;align-items:center;justify-content:space-between;padding:14px;background:#f8fafc;border-radius:10px;margin-bottom:12px;">
                    <div>
                        <p style="font-size:13px;font-weight:600;color:#334155;margin:0;">Enable Push</p>
                        <p style="font-size:11px;color:#94a3b8;margin:2px 0 0;">Show desktop popup notifications</p>
                    </div>
                    <button @click="togglePush()" style="position:relative;width:44px;height:24px;border-radius:12px;border:none;cursor:pointer;transition:background .2s;padding:0;"
                        :style="pushEnabled ? 'background:#6366f1' : 'background:#cbd5e1'">
                        <span style="position:absolute;top:2px;width:20px;height:20px;background:#fff;border-radius:50%;box-shadow:0 1px 3px rgba(0,0,0,.2);transition:left .2s;"
                            :style="pushEnabled ? 'left:22px' : 'left:2px'"></span>
                    </button>
                </div>

                {{-- Permission Status --}}
                <div style="padding:10px 14px;border-radius:10px;margin-bottom:12px;font-size:12px;display:flex;align-items:center;gap:6px;"
                    :style="pushPermission === 'granted' ? 'background:#f0fdf4;color:#16a34a;' : (pushPermission === 'denied' ? 'background:#fef2f2;color:#dc2626;' : 'background:#fffbeb;color:#d97706;')">
                    <span x-text="pushPermission === 'granted' ? '✅' : (pushPermission === 'denied' ? '❌' : '⚠️')"></span>
                    <span>
                        Browser permission:
                        <strong x-text="pushPermission === 'granted' ? 'Allowed' : (pushPermission === 'denied' ? 'Blocked (change in browser settings)' : 'Not requested yet')"></strong>
                    </span>
                </div>

                <template x-if="pushPermission !== 'granted'">
                    <button @click="requestPush()"
                        style="width:100%;padding:10px;font-size:13px;font-weight:600;color:#3b82f6;background:#eff6ff;border:1px solid #dbeafe;border-radius:10px;cursor:pointer;transition:all .15s;margin-bottom:8px;"
                        onmouseover="this.style.background='#dbeafe'" onmouseout="this.style.background='#eff6ff'">
                        🔔 Allow Push Notifications
                    </button>
                </template>

                <button @click="testPush()"
                    style="width:100%;padding:10px;font-size:13px;font-weight:600;color:#6366f1;background:#eef2ff;border:1px solid #e0e7ff;border-radius:10px;cursor:pointer;transition:all .15s;"
                    onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                    📱 Test Push Notification
                </button>
            </div>

            {{-- Notification Preferences --}}
            <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#f3e8ff;">
                        <svg style="width:20px;height:20px;color:#8b5cf6;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>
                    </span>
                    <div>
                        <h3 style="font-size:15px;font-weight:700;color:#0f172a;margin:0;">Preferences</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">Configure notification behavior</p>
                    </div>
                </div>

                @php
                    $prefs = [
                        ['key' => 'ticket', 'label' => 'Support Tickets', 'desc' => 'New tickets & replies'],
                        ['key' => 'task', 'label' => 'Task Updates', 'desc' => 'Assigned, completed tasks'],
                        ['key' => 'lead', 'label' => 'Lead & CRM', 'desc' => 'New leads, deal changes'],
                        ['key' => 'system', 'label' => 'System Alerts', 'desc' => 'Updates, maintenance']
                    ];
                @endphp
                @foreach($prefs as $pref)
                    <div style="display:flex;align-items:center;justify-content:space-between;padding:10px 14px;background:#f8fafc;border-radius:8px;margin-bottom:6px;">
                        <div>
                            <p style="font-size:13px;font-weight:600;color:#334155;margin:0;">{{ $pref['label'] }}</p>
                            <p style="font-size:11px;color:#94a3b8;margin:1px 0 0;">{{ $pref['desc'] }}</p>
                        </div>
                        <div x-data="{ on: localStorage.getItem('trustedu_noti_{{ $pref['key'] }}') !== 'false' }">
                            <button @click="on = !on; localStorage.setItem('trustedu_noti_{{ $pref['key'] }}', on ? 'true' : 'false')"
                                style="position:relative;width:36px;height:20px;border-radius:10px;border:none;cursor:pointer;transition:background .2s;padding:0;"
                                :style="on ? 'background:#6366f1' : 'background:#cbd5e1'">
                                <span style="position:absolute;top:2px;width:16px;height:16px;background:#fff;border-radius:50%;box-shadow:0 1px 2px rgba(0,0,0,.2);transition:left .2s;"
                                    :style="on ? 'left:18px' : 'left:2px'"></span>
                            </button>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Danger Zone --}}
            <div style="background:#fff;border:1px solid #fecaca;border-radius:16px;padding:24px;box-shadow:0 1px 3px rgba(0,0,0,.04);">
                <div style="display:flex;align-items:center;gap:10px;margin-bottom:16px;">
                    <span style="display:inline-flex;align-items:center;justify-content:center;width:40px;height:40px;border-radius:10px;background:#fef2f2;">
                        <svg style="width:20px;height:20px;color:#ef4444;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" /></svg>
                    </span>
                    <div>
                        <h3 style="font-size:15px;font-weight:700;color:#dc2626;margin:0;">Danger Zone</h3>
                        <p style="font-size:12px;color:#94a3b8;margin:2px 0 0;">Irreversible actions</p>
                    </div>
                </div>

                <button wire:click="deleteAllRead" wire:confirm="Are you sure? This will delete all READ notifications."
                    style="width:100%;padding:10px;font-size:13px;font-weight:600;color:#dc2626;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;cursor:pointer;margin-bottom:8px;transition:all .15s;"
                    onmouseover="this.style.background='#fee2e2'" onmouseout="this.style.background='#fef2f2'">
                    🗑 Delete All Read Notifications
                </button>

                <button wire:click="deleteAll" wire:confirm="⚠️ DELETE ALL NOTIFICATIONS? This cannot be undone!"
                    style="width:100%;padding:10px;font-size:13px;font-weight:600;color:#fff;background:#dc2626;border:none;border-radius:10px;cursor:pointer;transition:all .15s;"
                    onmouseover="this.style.background='#b91c1c'" onmouseout="this.style.background='#dc2626'">
                    ⚠️ Delete ALL Notifications
                </button>
            </div>
        </div>

    {{-- ─── Notification List ─── --}}
    @else
        <div style="background:#fff;border:1px solid #f1f5f9;border-radius:16px;box-shadow:0 1px 3px rgba(0,0,0,.04);overflow:hidden;">
            @forelse($this->notifications as $notification)
                @php
                    $colors = ['primary'=>['#eef2ff','#4f46e5'],'success'=>['#f0fdf4','#16a34a'],'warning'=>['#fffbeb','#d97706'],'danger'=>['#fef2f2','#dc2626'],'info'=>['#eff6ff','#2563eb']];
                    $c = $colors[$notification->color] ?? $colors['primary'];
                    $typeLabels = ['ticket_new'=>'New Ticket','ticket_reply'=>'Reply','task_assigned'=>'Task','lead_update'=>'Lead','system'=>'System'];
                @endphp
                <div style="display:flex;align-items:flex-start;gap:12px;padding:14px 20px;border-bottom:1px solid #f8fafc;transition:background .15s;
                    {{ $notification->isRead() ? '' : 'background:#fafaff;' }}"
                    onmouseover="this.style.background='#f8fafc'" onmouseout="this.style.background='{{ $notification->isRead() ? '#fff' : '#fafaff' }}'">

                    {{-- Icon --}}
                    <span style="flex-shrink:0;display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:{{ $c[0] }};margin-top:2px;">
                        @switch($notification->type)
                            @case('ticket_new')
                                <svg style="width:18px;height:18px;color:{{ $c[1] }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 6v.75m0 3v.75m0 3v.75m0 3V18m-9-5.25h5.25M7.5 15h3M3.375 5.25c-.621 0-1.125.504-1.125 1.125v3.026a2.999 2.999 0 010 5.198v3.026c0 .621.504 1.125 1.125 1.125h17.25c.621 0 1.125-.504 1.125-1.125v-3.026a2.999 2.999 0 010-5.198V6.375c0-.621-.504-1.125-1.125-1.125H3.375z" /></svg>
                                @break
                            @case('ticket_reply')
                                <svg style="width:18px;height:18px;color:{{ $c[1] }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 9.75a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375m-13.5 3.01c0 1.6 1.123 2.994 2.707 3.227 1.087.16 2.185.283 3.293.369V21l4.184-4.183a1.14 1.14 0 01.778-.332 48.294 48.294 0 005.83-.498c1.585-.233 2.708-1.626 2.708-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" /></svg>
                                @break
                            @case('task_assigned')
                                <svg style="width:18px;height:18px;color:{{ $c[1] }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                @break
                            @case('lead_update')
                                <svg style="width:18px;height:18px;color:{{ $c[1] }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" /></svg>
                                @break
                            @default
                                <svg style="width:18px;height:18px;color:{{ $c[1] }};" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                        @endswitch
                    </span>

                    {{-- Content --}}
                    <div style="flex:1;min-width:0;">
                        <div style="display:flex;align-items:start;justify-content:space-between;gap:8px;">
                            <div style="min-width:0;">
                                <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;">
                                    <p style="font-size:14px;font-weight:{{ $notification->isRead() ? '500' : '700' }};color:#0f172a;margin:0;">{{ $notification->title }}</p>
                                    <span style="font-size:10px;font-weight:600;padding:2px 6px;border-radius:4px;background:{{ $c[0] }};color:{{ $c[1] }};">
                                        {{ $typeLabels[$notification->type] ?? ucfirst($notification->type) }}
                                    </span>
                                    @if(!$notification->isRead())
                                        <span style="width:6px;height:6px;background:#6366f1;border-radius:50%;"></span>
                                    @endif
                                </div>
                                @if($notification->message)
                                    <p style="font-size:13px;color:#64748b;margin:3px 0 0;line-height:1.4;">{{ $notification->message }}</p>
                                @endif
                                <p style="font-size:11px;color:#94a3b8;margin:4px 0 0;">{{ $notification->created_at->format('M d, Y h:i A') }} · {{ $notification->created_at->diffForHumans() }}</p>
                            </div>

                            {{-- Actions --}}
                            <div style="display:flex;align-items:center;gap:2px;flex-shrink:0;">
                                @if($notification->action_url)
                                    <a href="{{ $notification->action_url }}"
                                        style="display:inline-flex;align-items:center;gap:3px;padding:5px 10px;font-size:11px;font-weight:600;color:#4f46e5;background:#eef2ff;border-radius:6px;text-decoration:none;transition:all .15s;"
                                        onmouseover="this.style.background='#e0e7ff'" onmouseout="this.style.background='#eef2ff'">
                                        {{ $notification->action_label ?? 'View' }}
                                    </a>
                                @endif
                                @if(!$notification->isRead())
                                    <button wire:click="markAsRead('{{ $notification->id }}')" title="Mark read"
                                        style="padding:5px;border-radius:6px;border:none;background:transparent;cursor:pointer;color:#94a3b8;transition:all .15s;"
                                        onmouseover="this.style.color='#16a34a';this.style.background='#f0fdf4'" onmouseout="this.style.color='#94a3b8';this.style.background='transparent'">
                                        <svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" /></svg>
                                    </button>
                                @else
                                    <button wire:click="markAsUnread('{{ $notification->id }}')" title="Mark unread"
                                        style="padding:5px;border-radius:6px;border:none;background:transparent;cursor:pointer;color:#94a3b8;transition:all .15s;"
                                        onmouseover="this.style.color='#f59e0b';this.style.background='#fffbeb'" onmouseout="this.style.color='#94a3b8';this.style.background='transparent'">
                                        <svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" /></svg>
                                    </button>
                                @endif
                                <button wire:click="deleteNotification('{{ $notification->id }}')" title="Delete"
                                    style="padding:5px;border-radius:6px;border:none;background:transparent;cursor:pointer;color:#94a3b8;transition:all .15s;"
                                    onmouseover="this.style.color='#dc2626';this.style.background='#fef2f2'" onmouseout="this.style.color='#94a3b8';this.style.background='transparent'">
                                    <svg style="width:16px;height:16px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div style="padding:60px 20px;text-align:center;">
                    <svg style="width:56px;height:56px;margin:0 auto 12px;color:#e2e8f0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" /></svg>
                    <h3 style="font-size:17px;font-weight:700;color:#94a3b8;margin:0 0 4px;">
                        @if($activeTab === 'unread') You're all caught up! 🎉
                        @elseif($activeTab === 'read') No read notifications
                        @elseif($search) No results for "{{ $search }}"
                        @else No notifications yet
                        @endif
                    </h3>
                    <p style="font-size:13px;color:#cbd5e1;margin:0;">
                        @if($activeTab === 'unread') All notifications have been read.
                        @elseif($search) Try a different search term.
                        @else Notifications will appear here when events happen.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($this->notifications->hasPages())
            <div style="margin-top:16px;">
                {{ $this->notifications->links() }}
            </div>
        @endif
    @endif
</div>
</x-filament-panels::page>
