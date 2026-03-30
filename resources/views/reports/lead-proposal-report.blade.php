<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Proposal — {{ $pd['title'] ?? 'Business Proposal' }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins','Arial',sans-serif;color:#1a1a1a;background:#666;font-size:10px;line-height:1.4}

        .page{width:210mm;min-height:297mm;margin:16px auto;background:#fff;padding:0;box-shadow:0 2px 16px rgba(0,0,0,.3);position:relative;overflow:hidden}

        /* ── TOOLBAR ── */
        .toolbar{position:fixed;top:0;left:0;right:0;background:#111;color:#fff;padding:10px 24px;display:flex;justify-content:space-between;align-items:center;z-index:999;font-size:13px}
        .toolbar button,.toolbar a{padding:8px 20px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit;text-decoration:none;display:inline-block}
        .toolbar .btn-print{background:#fff;color:#111}
        .toolbar .btn-back{background:transparent;color:#aaa;border:1px solid #555}
        .toolbar .btn-back:hover{color:#fff;border-color:#888}

        /* ── HEADER ── */
        .hdr{padding:24px 32px 16px;display:flex;justify-content:space-between;align-items:flex-start;border-bottom:3px solid #1a1a1a}
        .hdr-left{display:flex;align-items:center;gap:14px}
        .hdr-logo{width:50px;height:50px;border-radius:8px;background:#1a1a1a;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:18px}
        .hdr-logo img{width:100%;height:100%;border-radius:8px;object-fit:cover}
        .hdr-company h1{font-size:16px;font-weight:800;letter-spacing:-.02em;text-transform:uppercase;line-height:1.1}
        .hdr-company p{font-size:8px;color:#666;margin-top:1px}
        .hdr-right{text-align:right;font-size:8px;color:#555;line-height:1.5}

        /* ── TITLE BANNER ── */
        .title-bar{background:#1a1a1a;color:#fff;padding:12px 32px;text-align:center}
        .title-bar h2{font-size:16px;font-weight:700;text-transform:uppercase;letter-spacing:.08em}
        .title-bar p{font-size:10px;font-weight:300;margin-top:2px;opacity:.85}

        /* ── BODY ── */
        .body{padding:16px 32px 40px}

        /* Section */
        .sec{margin-bottom:12px}
        .sec-title{font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.08em;border-bottom:2px solid #1a1a1a;padding-bottom:3px;margin-bottom:8px;color:#1a1a1a}

        /* Prepared For/By Grid */
        .prep-grid{display:flex;gap:20px;margin-bottom:16px}
        .prep-box{flex:1;padding:12px;border:1px solid #e5e5e5;border-radius:6px}
        .prep-box h3{font-size:8px;text-transform:uppercase;letter-spacing:.1em;color:#999;margin-bottom:6px;font-weight:600}
        .prep-box .name{font-size:12px;font-weight:700;color:#1a1a1a}
        .prep-box p{font-size:9px;color:#555;margin-top:1px}

        /* Letter body */
        .letter-text{font-size:10px;line-height:1.6;color:#333;margin-bottom:10px}
        .letter-text p{margin-bottom:6px}

        /* Module list */
        .mod-grid{display:flex;flex-wrap:wrap;gap:4px;margin:6px 0 10px}
        .mod-chip{background:#f0f0f0;padding:3px 10px;border-radius:3px;font-size:9px;font-weight:500;border:1px solid #ddd}

        /* Features */
        .feat-table{width:100%;border-collapse:collapse;margin:6px 0}
        .feat-table td{padding:5px 8px;border-bottom:1px solid #eee;font-size:9.5px;vertical-align:top}
        .feat-table td:first-child{font-weight:600;width:35%;color:#1a1a1a}
        .feat-table td:last-child{color:#555}

        /* Tech spec */
        .tech-grid{display:grid;grid-template-columns:1fr 1fr;gap:4px;margin:6px 0}
        .tech-item{display:flex;justify-content:space-between;padding:4px 8px;background:#f8f8f8;border-radius:3px;font-size:9px}
        .tech-item span:first-child{font-weight:600;color:#1a1a1a}
        .tech-item span:last-child{color:#555}

        /* Cost table */
        .cost-table{width:60%;border-collapse:collapse;margin:8px 0}
        .cost-table td{padding:5px 10px;font-size:10px;border-bottom:1px solid #eee}
        .cost-table td:first-child{font-weight:500;color:#555}
        .cost-table td:last-child{text-align:right;font-weight:600;color:#1a1a1a}
        .cost-table tr.total{background:#1a1a1a;color:#fff}
        .cost-table tr.total td{font-weight:700;font-size:12px;border:none;padding:6px 10px}

        /* Terms list */
        .terms-list{list-style:none;margin:4px 0}
        .terms-list li{padding:2px 0 2px 14px;font-size:9px;color:#444;position:relative}
        .terms-list li::before{content:"•";position:absolute;left:0;color:#1a1a1a;font-weight:700}

        /* Signature */
        .sig-section{margin-top:24px;display:flex;justify-content:space-between;gap:30px}
        .sig-box{flex:1}
        .sig-line{border-top:1.5px solid #1a1a1a;margin-top:36px;padding-top:5px;font-size:9px;font-weight:700}
        .sig-sub{font-size:8px;color:#666;margin-top:1px}

        /* Validity */
        .validity-bar{margin-top:14px;padding:8px 12px;background:#f8f8f8;border-radius:4px;font-size:9px;color:#555;text-align:center;border:1px solid #eee}

        /* Footer */
        .rpt-footer{position:absolute;bottom:0;left:0;right:0;padding:6px 32px;border-top:1.5px solid #1a1a1a;display:flex;justify-content:space-between;font-size:7px;color:#999}

        @media print{
            body{background:#fff}
            .toolbar{display:none!important}
            .page{margin:0;box-shadow:none;width:100%;min-height:auto}
            @page{size:A4;margin:6mm}
        }
    </style>
</head>
<body>
    @php
        $pd = $lead->proposal_data ?? [];
        $report = $lead->contact_report ?? [];
        $modules = $pd['modules'] ?? [];
        $features = $pd['features'] ?? [];
        $base = (float)($pd['base_price'] ?? 0);
        $disc = (float)($pd['discount_percent'] ?? 0);
        $discAmt = $base * $disc / 100;
        $final = max(0, $base - $discAmt);
        $setup = (float)($pd['setup_cost'] ?? 0);
        $monthly = (float)($pd['monthly_fee'] ?? 0);
        $leadId = 'P-' . str_pad($lead->id, 4, '0', STR_PAD_LEFT);
        $team = $lead->team;
        $companyName = $team?->company_name ?: 'Trust Innovation Ltd.';
        $companySlogan = $team?->slogan ?: 'Enterprise Resource Planning Solutions';
        $companyPhone = $team?->company_phone ?: '';
        $companyEmail = $team?->company_email ?: '';
        $companyAddress = $team?->company_address ?: '';
        $companyWebsite = $team?->company_website ?: '';
        $logoPath = $team?->logo ? asset('storage/' . $team->logo) : null;
        $validUntil = now()->addDays((int)($pd['validity_days'] ?? 15))->format('d M Y');
    @endphp

    {{-- TOOLBAR --}}
    <div class="toolbar">
        <span>📄 Proposal — {{ $pd['title'] ?? 'Business Proposal' }}</span>
        <div style="display:flex;gap:8px">
            <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
            <button class="btn-back" onclick="window.close()">✕ Close</button>
        </div>
    </div>
    <div style="height:55px"></div>

    <div class="page">
        {{-- HEADER --}}
        <div class="hdr">
            <div class="hdr-left">
                <div class="hdr-logo">
                    @if($logoPath)<img src="{{ $logoPath }}" alt="Logo">@else{{ substr($companyName,0,2) }}@endif
                </div>
                <div class="hdr-company">
                    <h1>{{ $companyName }}</h1>
                    <p>{{ $companySlogan }}</p>
                    @if($companyAddress)<p>{{ $companyAddress }}</p>@endif
                </div>
            </div>
            <div class="hdr-right">
                @if($companyPhone)<div>📞 {{ $companyPhone }}</div>@endif
                @if($companyEmail)<div>✉️ {{ $companyEmail }}</div>@endif
                @if($companyWebsite)<div>🌐 {{ $companyWebsite }}</div>@endif
                <div style="margin-top:4px;font-weight:600;color:#1a1a1a">{{ $leadId }}</div>
            </div>
        </div>

        {{-- TITLE BANNER --}}
        <div class="title-bar">
            <h2>Business Proposal</h2>
            <p>{{ $pd['title'] ?? 'School Management Software (eduERP)' }}</p>
        </div>

        {{-- BODY --}}
        <div class="body">

            {{-- Prepared For / By --}}
            <div class="prep-grid">
                <div class="prep-box">
                    <h3>Prepared For</h3>
                    <div class="name">{{ $pd['client_name'] ?? $lead->name }}</div>
                    @if(!empty($pd['contact_person']))<p>{{ $pd['contact_person'] }}{{ !empty($pd['contact_designation']) ? ', '.$pd['contact_designation'] : '' }}</p>@endif
                    @if(!empty($pd['client_address']))<p>{{ $pd['client_address'] }}</p>@endif
                </div>
                <div class="prep-box">
                    <h3>Prepared By</h3>
                    <div class="name">{{ $pd['prepared_by'] ?? auth()->user()->name ?? '—' }}</div>
                    @if(!empty($pd['prepared_by_designation']))<p>{{ $pd['prepared_by_designation'] }}</p>@endif
                    <p>{{ $companyName }}</p>
                    <p style="margin-top:3px;font-weight:500;color:#1a1a1a">Date: {{ now()->format('d M Y') }}</p>
                </div>
            </div>

            {{-- Subject & Introduction --}}
            <div class="sec">
                @if(!empty($pd['subject']))<div class="sec-title">{{ $pd['subject'] }}</div>@endif
                <div class="letter-text">
                    <p>Dear Sir/Madam,</p>
                    @if(!empty($pd['introduction']))<p>{{ $pd['introduction'] }}</p>@endif
                </div>
            </div>

            {{-- Proposed Modules --}}
            @if(!empty($modules))
                <div class="sec">
                    <div class="sec-title">Proposed ERP Modules</div>
                    <div class="mod-grid">
                        @foreach($modules as $m)
                            <span class="mod-chip">✓ {{ $erpModules[$m] ?? $m }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Solution/Scope --}}
            @if(!empty($pd['solution_description']))
                <div class="sec">
                    <div class="sec-title">Scope of Work</div>
                    <div class="letter-text"><p>{{ $pd['solution_description'] }}</p></div>
                    @if(!empty($pd['implementation_days']))
                        <p style="font-size:10px;font-weight:600;margin-top:4px">Implementation Timeline: {{ $pd['implementation_days'] }} Days</p>
                    @endif
                </div>
            @endif

            {{-- Key Features --}}
            @if(!empty($features))
                <div class="sec">
                    <div class="sec-title">Key Features</div>
                    <table class="feat-table">
                        @foreach($features as $f)
                            @if(!empty($f['title']))
                                <tr>
                                    <td>{{ $f['title'] }}</td>
                                    <td>{{ $f['description'] ?? '' }}</td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
            @endif

            {{-- Technical Specification --}}
            <div class="sec">
                <div class="sec-title">Technical Specification</div>
                <div class="tech-grid">
                    <div class="tech-item"><span>Frontend</span><span>{{ $pd['tech_frontend'] ?? 'Web Application' }}</span></div>
                    <div class="tech-item"><span>Backend</span><span>{{ $pd['tech_backend'] ?? 'Secure ERP System' }}</span></div>
                    <div class="tech-item"><span>Database</span><span>{{ $pd['tech_database'] ?? 'MySQL' }}</span></div>
                    <div class="tech-item"><span>Access</span><span>{{ $pd['tech_access'] ?? 'Web + Mobile' }}</span></div>
                </div>
            </div>

            {{-- Cost Section --}}
            <div class="sec">
                <div class="sec-title">Investment Summary</div>
                <table class="cost-table">
                    @if($setup > 0)
                        <tr><td>One-Time Setup Fee</td><td>৳{{ number_format($setup, 2) }}</td></tr>
                    @endif
                    @if($monthly > 0)
                        <tr><td>Monthly Service Fee</td><td>৳{{ number_format($monthly, 2) }}/month</td></tr>
                    @endif
                    @if($base > 0)
                        <tr><td>Total Project Cost</td><td>৳{{ number_format($base, 2) }}</td></tr>
                    @endif
                    @if($disc > 0)
                        <tr><td>Discount ({{ $disc }}%)</td><td style="color:#dc2626">- ৳{{ number_format($discAmt, 2) }}</td></tr>
                    @endif
                    @if($final > 0)
                        <tr class="total"><td>Net Amount Payable</td><td>৳{{ number_format($final, 2) }}</td></tr>
                    @endif
                </table>
            </div>

            {{-- Payment Terms --}}
            @if(!empty($pd['payment_terms']))
                <div class="sec">
                    <div class="sec-title">Payment Terms</div>
                    <ul class="terms-list">
                        @foreach(explode("\n", $pd['payment_terms']) as $term)
                            @if(trim($term))<li>{{ trim($term) }}</li>@endif
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Support Terms --}}
            @if(!empty($pd['support_terms']))
                <div class="sec">
                    <div class="sec-title">Support & Maintenance</div>
                    <ul class="terms-list">
                        @foreach(explode("\n", $pd['support_terms']) as $term)
                            @if(trim($term))<li>{{ trim($term) }}</li>@endif
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Validity --}}
            <div class="validity-bar">
                📅 This proposal is valid for <strong>{{ $pd['validity_days'] ?? 15 }} days</strong> from the date of issue (valid until <strong>{{ $validUntil }}</strong>).
            </div>

            {{-- Signature --}}
            <div class="sig-section">
                <div class="sig-box">
                    <div class="sig-line">Prepared By</div>
                    <div class="sig-sub">{{ $pd['prepared_by'] ?? auth()->user()->name ?? '' }}</div>
                    @if(!empty($pd['prepared_by_designation']))<div class="sig-sub">{{ $pd['prepared_by_designation'] }}</div>@endif
                    <div class="sig-sub">{{ $companyName }}</div>
                    @if($companyPhone)<div class="sig-sub">{{ $companyPhone }}</div>@endif
                </div>
                <div class="sig-box">
                    <div class="sig-line">Accepted By</div>
                    <div class="sig-sub">{{ $pd['contact_person'] ?? $lead->name }}</div>
                    <div class="sig-sub">{{ $pd['client_name'] ?? '' }}</div>
                </div>
            </div>
        </div>

        {{-- FOOTER --}}
        <div class="rpt-footer">
            <span>{{ $companyName }} — {{ $companySlogan }}</span>
            <span>Generated: {{ now()->format('d M Y, h:i A') }}</span>
        </div>
    </div>
</body>
</html>
