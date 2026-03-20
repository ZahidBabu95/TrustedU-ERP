<x-filament-panels::page>
    @php
        $client = $this->record;
        $client->loadMissing(['teams', 'erpModules', 'monthlyStats']);

        $typeLabels = [
            'school' => 'School',
            'college' => 'College',
            'school_and_college' => 'School & College',
            'university' => 'University',
            'madrasha' => 'Madrasha',
            'coaching' => 'Coaching Center',
            'corporate' => 'Corporate',
            'ngo' => 'NGO',
            'other' => 'Other',
        ];
    @endphp

    <style>
        .cp-grid { display: grid; gap: 1.25rem; }
        .cp-grid-main { grid-template-columns: 1fr; }
        .cp-grid-stats { grid-template-columns: repeat(2, 1fr); }
        @media (min-width: 1024px) {
            .cp-grid-main { grid-template-columns: 2fr 1fr; }
            .cp-grid-stats { grid-template-columns: repeat(4, 1fr); }
        }
        .cp-card {
            background: white;
            border-radius: 0.75rem;
            border: 1px solid rgba(0,0,0,0.05);
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .dark .cp-card { background: rgb(17 24 39); border-color: rgb(31 41 55); }
        .cp-card-dark {
            background: linear-gradient(135deg, #4338ca 0%, #6366f1 100%);
            border-radius: 0.75rem;
            color: white;
            position: relative;
            overflow: hidden;
        }
        .cp-card-dark::before {
            content: '';
            position: absolute;
            top: -2rem;
            right: -2rem;
            width: 6rem;
            height: 6rem;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
            filter: blur(1.5rem);
        }
        .cp-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 2px; }
        .cp-val { font-size: 1.75rem; font-weight: 900; color: #0f172a; }
        .dark .cp-val { color: #f1f5f9; }
        .cp-badge { display: inline-flex; align-items: center; padding: 2px 8px; font-size: 10px; font-weight: 700; text-transform: uppercase; border-radius: 9999px; border: 1px solid; }
        .cp-badge-green { background: #ecfdf5; color: #059669; border-color: #d1fae5; }
        .cp-badge-red { background: #fef2f2; color: #ef4444; border-color: #fecaca; }
        .cp-badge-blue { background: #eef2ff; color: #4f46e5; border-color: #e0e7ff; }
        .cp-badge-amber { background: #fffbeb; color: #d97706; border-color: #fde68a; }
        .cp-section-title { font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: 0.1em; color: #0f172a; margin-bottom: 0.75rem; }
        .dark .cp-section-title { color: #f1f5f9; }
        .cp-row { display: flex; align-items: center; gap: 0.75rem; padding: 0.5rem 0; }
        .cp-row-icon { width: 18px; height: 18px; color: #94a3b8; flex-shrink: 0; }
        .cp-row-text { font-size: 0.875rem; font-weight: 500; color: #475569; }
        .dark .cp-row-text { color: #cbd5e1; }
        .cp-row-muted { font-size: 0.875rem; color: #cbd5e1; }
        .cp-list-item { display: flex; align-items: center; justify-content: space-between; padding: 0.6rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.03); transition: background 0.15s; }
        .cp-list-item:hover { background: rgba(0,0,0,0.015); }
        .dark .cp-list-item { border-color: rgb(31 41 55); }
        .dark .cp-list-item:hover { background: rgba(255,255,255,0.03); }
        .cp-list-item:last-child { border-bottom: none; }
        .cp-progress-bar { width: 100%; height: 6px; background: #f1f5f9; border-radius: 9999px; overflow: hidden; }
        .dark .cp-progress-bar { background: #1f2937; }
        .cp-module-icon { width: 2rem; height: 2rem; border-radius: 0.5rem; background: #eef2ff; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .dark .cp-module-icon { background: rgba(79,70,229,0.15); }
        .cp-team-item { display: flex; align-items: center; gap: 0.6rem; padding: 0.5rem 0.75rem; background: #f8fafc; border-radius: 0.5rem; }
        .dark .cp-team-item { background: rgba(255,255,255,0.04); }
        .cp-table th { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; padding: 0.6rem 1.25rem; background: #f8fafc; border-bottom: 1px solid #f1f5f9; }
        .dark .cp-table th { background: rgba(255,255,255,0.03); border-color: #1f2937; }
        .cp-table td { padding: 0.6rem 1.25rem; font-size: 0.875rem; border-bottom: 1px solid #f8fafc; }
        .dark .cp-table td { border-color: #1f2937; }
    </style>

    <div style="display: flex; flex-direction: column; gap: 1.25rem;">

        {{-- ═══ HERO HEADER ═══ --}}
        <div style="display: flex; flex-wrap: wrap; align-items: flex-end; justify-content: space-between; gap: 1rem;">
            <div style="display: flex; align-items: flex-start; gap: 1rem;">
                <div style="width: 56px; height: 56px; border-radius: 0.75rem; border: 1px solid rgba(0,0,0,0.06); overflow: hidden; display: flex; align-items: center; justify-content: center; background: white; flex-shrink: 0;"
                     class="dark:bg-gray-800 dark:border-gray-700">
                    @if($client->logo_url)
                        <img src="{{ $client->logo_url }}" alt="{{ $client->name }}" style="width: 48px; height: 48px; object-fit: contain;" />
                    @else
                        <span style="font-size: 1.25rem; font-weight: 900; color: #4f46e5;">{{ strtoupper(substr($client->name, 0, 2)) }}</span>
                    @endif
                </div>
                <div>
                    <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap; margin-bottom: 2px;">
                        <h2 style="font-size: 1.25rem; font-weight: 800; margin: 0;" class="text-gray-900 dark:text-white">{{ $client->name }}</h2>
                        @if($client->institution_type)
                            <span class="cp-badge cp-badge-blue">{{ $typeLabels[$client->institution_type] ?? ucfirst($client->institution_type) }}</span>
                        @endif
                        @if($client->is_active)
                            <span class="cp-badge cp-badge-green">Active</span>
                        @else
                            <span class="cp-badge cp-badge-red">Inactive</span>
                        @endif
                    </div>
                    <p style="font-size: 0.8rem; color: #94a3b8; display: flex; align-items: center; gap: 0.5rem;">
                        ID: {{ $client->client_id ?? '—' }}
                        @if($client->district)
                            <span style="color: #e2e8f0;">•</span> {{ $client->district }}
                        @endif
                        @if($client->is_live)
                            <span style="color: #e2e8f0;">•</span>
                            <span style="color: #10b981; font-weight: 600;">● Live</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>

        {{-- ═══ MAIN 2-COL GRID ═══ --}}
        <div class="cp-grid cp-grid-main">

            {{-- ─── LEFT COLUMN ─── --}}
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">

                {{-- Quick Stats --}}
                <div class="cp-grid cp-grid-stats">
                    <div class="cp-card" style="padding: 1rem; text-align: center;">
                        <p class="cp-label">Teams</p>
                        <p class="cp-val">{{ $client->teams->count() }}</p>
                    </div>
                    <div class="cp-card" style="padding: 1rem; text-align: center;">
                        <p class="cp-label">ERP Modules</p>
                        <p class="cp-val" style="color: #4f46e5;">{{ $client->erpModules->count() }}</p>
                    </div>
                    <div class="cp-card" style="padding: 1rem; text-align: center;">
                        <p class="cp-label">Active Students</p>
                        <p class="cp-val" style="color: #059669;">{{ $client->latest_student_count ? number_format($client->latest_student_count) : '—' }}</p>
                    </div>
                    <div class="cp-card" style="padding: 1rem; text-align: center;">
                        <p class="cp-label">Contract</p>
                        @if($client->contract_end)
                            @php $daysLeft = now()->diffInDays($client->contract_end, false); @endphp
                            <p class="cp-val" style="color: {{ $daysLeft > 30 ? '#059669' : ($daysLeft > 0 ? '#d97706' : '#ef4444') }};">
                                {{ $daysLeft > 0 ? $daysLeft . 'd' : 'Exp' }}
                            </p>
                        @else
                            <p class="cp-val" style="color: #cbd5e1;">—</p>
                        @endif
                    </div>
                </div>

                {{-- ERP Modules --}}
                <div class="cp-card" style="overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.04); display: flex; align-items: center; gap: 0.5rem;">
                        <x-heroicon-o-squares-2x2 style="width: 18px; height: 18px; color: #4f46e5;" />
                        <span style="font-size: 0.9rem; font-weight: 700;" class="text-gray-900 dark:text-white">ERP Modules</span>
                        <span class="cp-badge cp-badge-blue">{{ $client->erpModules->count() }}</span>
                    </div>
                    @if($client->erpModules->count())
                        @foreach($client->erpModules as $module)
                            <div class="cp-list-item">
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    <div class="cp-module-icon">
                                        <x-heroicon-o-cube style="width: 14px; height: 14px; color: #4f46e5;" />
                                    </div>
                                    <div>
                                        <p style="font-size: 0.85rem; font-weight: 700; margin: 0;" class="text-gray-900 dark:text-white">{{ $module->name }}</p>
                                        @if($module->pivot->activated_at)
                                            <p style="font-size: 10px; color: #94a3b8; margin: 0;">Since {{ \Carbon\Carbon::parse($module->pivot->activated_at)->format('d M Y') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <span class="cp-badge {{ $module->pivot->is_active ? 'cp-badge-green' : 'cp-badge-red' }}">
                                    {{ $module->pivot->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div style="padding: 2.5rem 1rem; text-align: center;">
                            <x-heroicon-o-squares-2x2 style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #e2e8f0;" />
                            <p style="font-size: 0.85rem; color: #94a3b8;">No modules assigned yet.</p>
                        </div>
                    @endif
                </div>

                {{-- Monthly Student Stats --}}
                <div class="cp-card" style="overflow: hidden;">
                    <div style="padding: 1rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.04); display: flex; align-items: center; gap: 0.5rem;">
                        <x-heroicon-o-academic-cap style="width: 18px; height: 18px; color: #059669;" />
                        <span style="font-size: 0.9rem; font-weight: 700;" class="text-gray-900 dark:text-white">Monthly Student Statistics</span>
                    </div>
                    @if($client->monthlyStats->count())
                        <table class="cp-table" style="width: 100%; border-collapse: collapse;">
                            <thead>
                                <tr>
                                    <th style="text-align: left;">Period</th>
                                    <th style="text-align: left;">Active</th>
                                    <th style="text-align: left;">Total</th>
                                    <th style="text-align: left;">Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($client->monthlyStats->take(6) as $stat)
                                    <tr>
                                        <td><span class="cp-badge cp-badge-blue">{{ $stat->month_name }} {{ $stat->year }}</span></td>
                                        <td style="font-weight: 700; color: #059669;">{{ number_format($stat->active_students) }}</td>
                                        <td class="cp-row-text">{{ number_format($stat->total_students) }}</td>
                                        <td style="font-size: 0.8rem; color: #94a3b8; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">{{ $stat->notes ?? '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        <div style="padding: 2.5rem 1rem; text-align: center;">
                            <x-heroicon-o-academic-cap style="width: 2rem; height: 2rem; margin: 0 auto 0.5rem; color: #e2e8f0;" />
                            <p style="font-size: 0.85rem; color: #94a3b8;">No student statistics yet.</p>
                        </div>
                    @endif
                </div>

                {{-- Domain & Hosting --}}
                @if($client->domain_name || $client->hosting_provider)
                    <div class="cp-card" style="overflow: hidden;">
                        <div style="padding: 1rem 1.25rem; border-bottom: 1px solid rgba(0,0,0,0.04); display: flex; align-items: center; gap: 0.5rem;">
                            <x-heroicon-o-globe-alt style="width: 18px; height: 18px; color: #6366f1;" />
                            <span style="font-size: 0.9rem; font-weight: 700;" class="text-gray-900 dark:text-white">Domain & Hosting</span>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0;">
                            {{-- Domain Side --}}
                            <div style="padding: 1rem 1.25rem; border-right: 1px solid rgba(0,0,0,0.04);">
                                <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 0.75rem;">Domain</p>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">Domain</span>
                                        <span style="font-size: 0.85rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">{{ $client->domain_name ?? '—' }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">Provider</span>
                                        <span style="font-size: 0.85rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">{{ $client->domain_provider ?? '—' }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">Expiry</span>
                                        @if($client->domain_expiry)
                                            @php $domainDays = now()->diffInDays($client->domain_expiry, false); @endphp
                                            <span class="cp-badge {{ $domainDays > 30 ? 'cp-badge-green' : ($domainDays > 0 ? 'cp-badge-amber' : 'cp-badge-red') }}">
                                                {{ $client->domain_expiry->format('d M Y') }}
                                                ({{ $domainDays > 0 ? $domainDays . 'd left' : 'Expired' }})
                                            </span>
                                        @else
                                            <span style="font-size: 0.85rem; color: #cbd5e1;">—</span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            {{-- Hosting Side --}}
                            <div style="padding: 1rem 1.25rem;">
                                <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: #94a3b8; margin-bottom: 0.75rem;">Hosting</p>
                                <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">Provider</span>
                                        <span style="font-size: 0.85rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">{{ $client->hosting_provider ?? '—' }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">Package</span>
                                        <span style="font-size: 0.85rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">{{ $client->hosting_package ?? '—' }}</span>
                                    </div>
                                    <div style="display: flex; justify-content: space-between; align-items: center;">
                                        <span style="font-size: 0.75rem; color: #94a3b8;">Expiry</span>
                                        @if($client->hosting_expiry)
                                            @php $hostDays = now()->diffInDays($client->hosting_expiry, false); @endphp
                                            <span class="cp-badge {{ $hostDays > 30 ? 'cp-badge-green' : ($hostDays > 0 ? 'cp-badge-amber' : 'cp-badge-red') }}">
                                                {{ $client->hosting_expiry->format('d M Y') }}
                                                ({{ $hostDays > 0 ? $hostDays . 'd left' : 'Expired' }})
                                            </span>
                                        @else
                                            <span style="font-size: 0.85rem; color: #cbd5e1;">—</span>
                                        @endif
                                    </div>
                                    @if($client->hosting_notes)
                                        <div style="margin-top: 0.25rem; padding: 0.5rem; background: #f8fafc; border-radius: 0.5rem;">
                                            <p style="font-size: 0.75rem; color: #64748b; line-height: 1.5; margin: 0;">{{ $client->hosting_notes }}</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ─── RIGHT COLUMN ─── --}}
            <div style="display: flex; flex-direction: column; gap: 1.25rem;">

                {{-- Contact Info --}}
                <div class="cp-card" style="padding: 1.25rem;">
                    <p class="cp-section-title">Contact Info</p>
                    <div style="display: flex; flex-direction: column; gap: 0.6rem;">
                        <div class="cp-row">
                            <x-heroicon-o-envelope class="cp-row-icon" />
                            <span class="{{ $client->email ? 'cp-row-text' : 'cp-row-muted' }}">{{ $client->email ?? '—' }}</span>
                        </div>
                        <div class="cp-row">
                            <x-heroicon-o-phone class="cp-row-icon" />
                            <span class="{{ $client->phone ? 'cp-row-text' : 'cp-row-muted' }}">{{ $client->phone ?? '—' }}</span>
                        </div>
                        <div class="cp-row">
                            <x-heroicon-o-globe-alt class="cp-row-icon" />
                            @if($client->website)
                                <a href="{{ $client->website }}" target="_blank" style="font-size: 0.875rem; font-weight: 500; color: #4f46e5; text-decoration: none;">{{ $client->website }}</a>
                            @else
                                <span class="cp-row-muted">—</span>
                            @endif
                        </div>
                        @if($client->address)
                            <div class="cp-row" style="align-items: flex-start;">
                                <x-heroicon-o-map-pin class="cp-row-icon" style="margin-top: 2px;" />
                                <span style="font-size: 0.85rem; color: #64748b; line-height: 1.5;">{{ $client->address }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Student Overview (Dark) --}}
                <div class="cp-card-dark" style="padding: 1.25rem;">
                    <p style="font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.5); margin-bottom: 0.75rem;">Student Overview</p>
                    <div style="position: relative; z-index: 1;">
                        <div style="margin-bottom: 1rem;">
                            <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.45); margin-bottom: 2px;">Latest Active</p>
                            <p style="font-size: 2rem; font-weight: 900;">{{ $client->latest_student_count ? number_format($client->latest_student_count) : '—' }}</p>
                        </div>
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem;">
                            <div>
                                <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.45); margin-bottom: 2px;">Active Modules</p>
                                <p style="font-size: 1.125rem; font-weight: 700;">{{ $client->erpModules->where('pivot.is_active', true)->count() }}</p>
                            </div>
                            <div>
                                <p style="font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: rgba(255,255,255,0.45); margin-bottom: 2px;">Stat Records</p>
                                <p style="font-size: 1.125rem; font-weight: 700;">{{ $client->monthlyStats->count() }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Principal / Head --}}
                @if($client->principal_name || $client->principal_phone)
                    <div class="cp-card" style="padding: 1.25rem;">
                        <p class="cp-section-title">Principal / Head</p>
                        <div class="cp-team-item">
                            <div class="cp-module-icon">
                                <x-heroicon-o-user-circle style="width: 18px; height: 18px; color: #4f46e5;" />
                            </div>
                            <div>
                                <p style="font-size: 0.85rem; font-weight: 700; margin: 0;" class="text-gray-900 dark:text-white">{{ $client->principal_name ?? '—' }}</p>
                                @if($client->principal_phone)
                                    <p style="font-size: 0.75rem; color: #94a3b8; margin: 0;">{{ $client->principal_phone }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Contract --}}
                <div class="cp-card" style="padding: 1.25rem;">
                    <p class="cp-section-title">Contract Details</p>
                    <div style="display: flex; flex-direction: column; gap: 0.5rem;">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.75rem; color: #94a3b8;">Start</span>
                            <span style="font-size: 0.85rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">{{ $client->contract_start?->format('d M Y') ?? '—' }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.75rem; color: #94a3b8;">End</span>
                            <span style="font-size: 0.85rem; font-weight: 600;" class="text-gray-700 dark:text-gray-300">{{ $client->contract_end?->format('d M Y') ?? '—' }}</span>
                        </div>
                        @if($client->contract_start && $client->contract_end)
                            @php
                                $totalDays = $client->contract_start->diffInDays($client->contract_end);
                                $elapsed = $client->contract_start->diffInDays(now());
                                $progress = $totalDays > 0 ? min(100, max(0, ($elapsed / $totalDays) * 100)) : 0;
                                $barColor = $progress > 90 ? '#ef4444' : ($progress > 70 ? '#d97706' : '#059669');
                            @endphp
                            <div style="margin-top: 0.25rem;">
                                <div class="cp-progress-bar">
                                    <div style="height: 100%; border-radius: 9999px; background: {{ $barColor }}; width: {{ $progress }}%;"></div>
                                </div>
                                <p style="font-size: 10px; color: #94a3b8; margin-top: 4px;">{{ round($progress) }}% elapsed</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Teams --}}
                <div class="cp-card" style="padding: 1.25rem;">
                    <p class="cp-section-title">Assigned Teams</p>
                    @if($client->teams->count())
                        <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                            @foreach($client->teams as $team)
                                <div class="cp-team-item">
                                    <div class="cp-module-icon">
                                        <x-heroicon-o-user-group style="width: 14px; height: 14px; color: #4f46e5;" />
                                    </div>
                                    <p style="font-size: 0.85rem; font-weight: 700; margin: 0;" class="text-gray-900 dark:text-white">{{ $team->name }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p style="font-size: 0.85rem; color: #94a3b8; text-align: center; padding: 0.75rem 0;">No teams assigned</p>
                    @endif
                </div>

                {{-- System Info --}}
                <div class="cp-card" style="padding: 1.25rem;">
                    <p class="cp-section-title">System</p>
                    <div style="display: flex; flex-direction: column; gap: 0.4rem;">
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.75rem; color: #94a3b8;">Created</span>
                            <span style="font-size: 0.75rem; font-weight: 500; color: #64748b;">{{ $client->created_at->format('d M Y, h:i A') }}</span>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span style="font-size: 0.75rem; color: #94a3b8;">Updated</span>
                            <span style="font-size: 0.75rem; font-weight: 500; color: #64748b;">{{ $client->updated_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-filament-panels::page>
