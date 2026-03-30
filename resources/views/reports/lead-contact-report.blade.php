<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Lead Contact Report — {{ $lead->name }}</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;color:#000;background:#888;font-size:10px;line-height:1.25}

        .page{width:210mm;min-height:297mm;margin:16px auto;background:#fff;padding:0;box-shadow:0 2px 16px rgba(0,0,0,.25);position:relative}

        /* ── TOOLBAR ── */
        .toolbar{position:fixed;top:0;left:0;right:0;background:#111;color:#fff;padding:10px 20px;display:flex;justify-content:space-between;align-items:center;z-index:999;font-size:13px}
        .toolbar button{padding:7px 18px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;font-family:inherit}
        .toolbar .btn-print{background:#fff;color:#111}
        .toolbar .btn-close{background:transparent;color:#aaa;border:1px solid #444}
        .toolbar .btn-close:hover{color:#fff;border-color:#888}

        /* ── HEADER ── */
        .rpt-header{border-bottom:2px solid #000;padding:12px 24px 8px;display:flex;justify-content:space-between;align-items:flex-start}
        .rpt-header-left h1{font-size:14px;font-weight:800;letter-spacing:-.03em;text-transform:uppercase}
        .rpt-header-left p{font-size:8px;color:#555;margin-top:0}
        .rpt-header-right{text-align:right;font-size:8px;color:#444;line-height:1.3}
        .rpt-header-right .rpt-id{font-size:12px;font-weight:800;color:#000;letter-spacing:.5px}

        .rpt-title-bar{background:#000;color:#fff;padding:5px 24px;font-size:11px;font-weight:700;letter-spacing:.04em;text-transform:uppercase}

        /* ── BODY ── */
        .rpt-body{padding:10px 24px 50px}

        /* Section */
        .sec{margin-bottom:8px}
        .sec-title{font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.08em;border-bottom:1.5px solid #000;padding-bottom:2px;margin-bottom:4px}

        /* Table layout */
        .info-table{width:100%;border-collapse:collapse}
        .info-table td{padding:2px 5px;vertical-align:top;font-size:9.5px;border:1px solid #ddd}
        .info-table .lbl{font-weight:700;color:#333;text-transform:uppercase;font-size:7.5px;letter-spacing:.05em;width:25%;background:#f5f5f5}
        .info-table .val{color:#000;font-weight:500}
        .info-table .val--bold{font-weight:700;font-size:10px}

        /* Full width text block */
        .text-block{border:1px solid #ddd;padding:4px 8px;margin-top:3px;font-size:9.5px;line-height:1.4;color:#111;background:#fafafa}
        .text-block-label{font-size:7.5px;font-weight:700;text-transform:uppercase;letter-spacing:.05em;color:#333;margin-bottom:1px}

        /* Chips */
        .chip-list{display:flex;flex-wrap:wrap;gap:3px;margin-top:2px}
        .chip{border:1px solid #000;padding:1px 6px;font-size:8px;font-weight:600;border-radius:2px}

        /* Conversion bar */
        .conv-bar-wrap{height:8px;background:#eee;border:1px solid #ccc;margin-top:3px}
        .conv-bar-fill{height:100%;background:#000}

        /* Recommendation */
        .rec-box{border:2px solid #000;padding:6px 10px;margin-top:6px}
        .rec-box h4{font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;margin-bottom:3px;border-bottom:1px solid #000;padding-bottom:2px}
        .rec-box li{font-size:9px;padding:1px 0;padding-left:10px;position:relative;list-style:none}
        .rec-box li::before{content:'▸';position:absolute;left:0;font-weight:700}

        /* Footer */
        .rpt-footer{position:absolute;bottom:0;left:0;right:0;border-top:2px solid #000;padding:6px 24px;display:flex;justify-content:space-between;align-items:flex-end;font-size:7px;color:#444}
        .sig-block{display:flex;gap:30px}
        .sig-item{text-align:center}
        .sig-line{width:90px;border-bottom:1px solid #000;margin-bottom:2px;height:50px}
        .sig-label{font-size:7px;font-weight:700;text-transform:uppercase;letter-spacing:.06em}

        /* Watermark */
        .watermark{position:absolute;top:50%;left:50%;transform:translate(-50%,-50%) rotate(-35deg);font-size:80px;font-weight:800;color:rgba(0,0,0,.03);letter-spacing:.1em;pointer-events:none;white-space:nowrap}

        @media print{
            body{background:#fff}
            .page{margin:0;box-shadow:none;width:100%;min-height:auto}
            .toolbar{display:none!important}
        }
        @page{size:A4;margin:0}
    </style>
</head>
<body>

<div class="toolbar">
    <span>📄 Lead Contact Report — {{ $lead->name }}</span>
    <div style="display:flex;gap:8px">
        <button class="btn-close" onclick="window.close()">✕ Close</button>
        <button class="btn-print" onclick="window.print()">🖨️ Print / Save as PDF</button>
    </div>
</div>

@php
    $r = $lead->contact_report ?? [];
    $budgetMap = [
        'under_3000' => 'Under ৳3,000',
        '3000_5000' => '৳3,000 – ৳5,000',
        '5000_10000' => '৳5,000 – ৳10,000',
        '10000_20000' => '৳10,000 – ৳20,000',
        '20000_50000' => '৳20,000 – ৳50,000',
        'above_50000' => '৳50,000+',
    ];
    $interestMap = [
        'very_interested' => 'Very Interested',
        'somewhat_interested' => 'Somewhat Interested',
        'just_exploring' => 'Just Exploring',
        'not_interested' => 'Not Interested',
    ];
    $assigneeName = $lead->assignee ? $lead->assignee->name : '—';
    $companyName = $lead->team?->company_name ?: ($lead->team?->name ?: 'Trust Innovation Ltd.');
    $companySlogan = $lead->team?->slogan ?: 'Enterprise Resource Planning Solutions';
@endphp

<div style="padding-top:50px">
<div class="page">
    <div class="watermark">CONFIDENTIAL</div>

    {{-- HEADER --}}
    <div class="rpt-header">
        <div class="rpt-header-left">
            <h1>{{ $companyName }}</h1>
            <p>{{ $companySlogan }}</p>
        </div>
        <div class="rpt-header-right">
            <div class="rpt-id">LCR-{{ str_pad($lead->id, 4, '0', STR_PAD_LEFT) }}</div>
            <div>{{ now()->format('d M Y, h:i A') }}</div>
            <div>Prepared by: {{ $assigneeName }}</div>
        </div>
    </div>

    <div class="rpt-title-bar">Lead Contact Report</div>

    {{-- BODY --}}
    <div class="rpt-body">

        {{-- 1. LEAD OVERVIEW --}}
        <div class="sec">
            <div class="sec-title">1. Lead Overview</div>
            <table class="info-table">
                <tr>
                    <td class="lbl">Full Name</td>
                    <td class="val val--bold">{{ $lead->name }}</td>
                    <td class="lbl">Status</td>
                    <td class="val">{{ \App\Models\Lead::STATUS_LABELS[$lead->status] ?? ucfirst($lead->status) }}</td>
                </tr>
                <tr>
                    <td class="lbl">Email</td>
                    <td class="val">{{ $lead->email ?: '—' }}</td>
                    <td class="lbl">Phone</td>
                    <td class="val">{{ $lead->phone ?: '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Lead Source</td>
                    <td class="val">{{ \App\Models\Lead::SOURCE_LABELS[$lead->source] ?? $lead->source ?? '—' }}</td>
                    <td class="lbl">Interest Level</td>
                    <td class="val">{{ $lead->interest_level ? ucfirst($lead->interest_level) : '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Lead Value</td>
                    <td class="val">{{ $lead->value ? '৳' . number_format($lead->value) : '—' }}</td>
                    <td class="lbl">Assigned To</td>
                    <td class="val">{{ $assigneeName }}</td>
                </tr>
                <tr>
                    <td class="lbl">Priority</td>
                    <td class="val">{{ \App\Models\Lead::PRIORITY_LABELS[$lead->priority] ?? $lead->priority ?? '—' }}</td>
                    <td class="lbl">Created</td>
                    <td class="val">{{ $lead->created_at ? $lead->created_at->format('d M Y') : '—' }}</td>
                </tr>
            </table>
        </div>

        @if(!empty($r))
        {{-- 2. INSTITUTION DETAILS --}}
        <div class="sec">
            <div class="sec-title">2. Institution Details</div>
            <table class="info-table">
                <tr>
                    <td class="lbl">Institution Name</td>
                    <td class="val val--bold">{{ $r['institution_name'] ?? '—' }}</td>
                    <td class="lbl">Type</td>
                    <td class="val">{{ \App\Models\Lead::INSTITUTE_TYPE_LABELS[$r['institution_type'] ?? ''] ?? ($r['institution_type'] ?? '—') }}</td>
                </tr>
                <tr>
                    <td class="lbl">Address</td>
                    <td class="val">{{ $r['institution_address'] ?? '—' }}</td>
                    <td class="lbl">No. of Students</td>
                    <td class="val val--bold">{{ $r['student_count'] ?? '—' }}</td>
                </tr>
            </table>
        </div>

        {{-- 3. DECISION MAKER --}}
        <div class="sec">
            <div class="sec-title">3. Decision Maker</div>
            <table class="info-table">
                <tr>
                    <td class="lbl">Name</td>
                    <td class="val val--bold">{{ $r['decision_maker_name'] ?? '—' }}</td>
                    <td class="lbl">Designation</td>
                    <td class="val">{{ $r['decision_maker_designation'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Phone</td>
                    <td class="val">{{ $r['decision_maker_phone'] ?? '—' }}</td>
                    <td class="lbl">&nbsp;</td>
                    <td class="val">&nbsp;</td>
                </tr>
            </table>
        </div>

        {{-- 4. CONVERSATION DETAILS --}}
        <div class="sec">
            <div class="sec-title">4. Conversation Details</div>
            <table class="info-table">
                <tr>
                    <td class="lbl">Contacted Person</td>
                    <td class="val">{{ $r['contacted_person'] ?? '—' }}</td>
                    <td class="lbl">Contact Date & Time</td>
                    <td class="val">{{ !empty($r['contacted_at']) ? \Carbon\Carbon::parse($r['contacted_at'])->format('d M Y, h:i A') : '—' }}</td>
                </tr>
            </table>
            @if(!empty($r['conversation_summary']))
            <div class="text-block">
                <div class="text-block-label">Conversation Summary</div>
                {{ $r['conversation_summary'] }}
            </div>
            @endif
        </div>

        {{-- 5. PREVIOUS SOFTWARE --}}
        <div class="sec">
            <div class="sec-title">5. Previous Software</div>
            <table class="info-table">
                <tr>
                    <td class="lbl">Used Software Before</td>
                    <td class="val">{{ !empty($r['has_previous_software']) ? 'Yes' : 'No' }}</td>
                    <td class="lbl">Software Name</td>
                    <td class="val">{{ $r['previous_software_name'] ?? '—' }}</td>
                </tr>
                <tr>
                    <td class="lbl">Reason for Switching</td>
                    <td class="val" colspan="3">{{ $r['switch_reason'] ?? '—' }}</td>
                </tr>
            </table>
        </div>

        {{-- 6. REQUIREMENTS & BUDGET --}}
        <div class="sec">
            <div class="sec-title">6. Requirements & Budget</div>
            <table class="info-table">
                <tr>
                    <td class="lbl">Monthly Budget Range</td>
                    <td class="val val--bold">{{ $budgetMap[$r['budget_range'] ?? ''] ?? ($r['budget_range'] ?? '—') }}</td>
                    <td class="lbl">Interest Assessment</td>
                    <td class="val">{{ $interestMap[$r['interest_assessment'] ?? ''] ?? ($r['interest_assessment'] ?? '—') }}</td>
                </tr>
            </table>

            @if(!empty($r['desired_modules']))
            <div style="margin-top:6px">
                <div class="text-block-label">Desired ERP Modules</div>
                <div class="chip-list">
                    @foreach($r['desired_modules'] as $mod)
                        <span class="chip">{{ $erpModules[$mod] ?? $mod }}</span>
                    @endforeach
                </div>
            </div>
            @endif

            @if(!empty($r['primary_needs']))
            <div class="text-block">
                <div class="text-block-label">Primary Needs & Pain Points</div>
                {{ $r['primary_needs'] }}
            </div>
            @endif
        </div>

        {{-- 7. CONVERSION ASSESSMENT --}}
        <div class="sec">
            <div class="sec-title">7. Conversion Assessment</div>
            <table class="info-table">
                <tr>
                    <td class="lbl">Conversion Probability</td>
                    <td class="val val--bold" style="font-size:12px">{{ $r['conversion_probability'] ?? '—' }}{{ !empty($r['conversion_probability']) ? '%' : '' }}</td>
                    <td class="lbl">Follow-up Date</td>
                    <td class="val">{{ !empty($r['follow_up_date']) ? \Carbon\Carbon::parse($r['follow_up_date'])->format('d M Y') : '—' }}</td>
                </tr>
            </table>
            @if(!empty($r['conversion_probability']))
            <div class="conv-bar-wrap">
                <div class="conv-bar-fill" style="width:{{ (int)$r['conversion_probability'] }}%"></div>
            </div>
            @endif
            @if(!empty($r['conversion_comment']))
            <div class="text-block">
                <div class="text-block-label">Conversion Comment / Reasoning</div>
                {{ $r['conversion_comment'] }}
            </div>
            @endif
        </div>

        {{-- 8. ADDITIONAL NOTES --}}
        @if(!empty($r['additional_notes']))
        <div class="sec">
            <div class="sec-title">8. Additional Notes</div>
            <div class="text-block">{{ $r['additional_notes'] }}</div>
        </div>
        @endif

        {{-- 9. RECOMMENDED ACTIONS --}}
        <div class="rec-box">
            <h4>Recommended Next Steps</h4>
            <ul>
                @php
                    $pct = (int) ($r['conversion_probability'] ?? 0);
                    $actions = [];
                    if ($pct >= 70) {
                        $actions[] = 'HIGH PRIORITY — Schedule product demo within 3 business days';
                        $actions[] = 'Prepare customized proposal based on module requirements';
                        $actions[] = 'Assign dedicated account manager for this lead';
                    } elseif ($pct >= 40) {
                        $actions[] = 'MODERATE PRIORITY — Maintain regular engagement';
                        $actions[] = 'Share case studies & references from similar institutions';
                        $actions[] = 'Address specific concerns raised during conversation';
                    } else {
                        $actions[] = 'LOW PRIORITY — Nurture through periodic follow-ups';
                        $actions[] = 'Share product updates and success stories';
                        $actions[] = 'Re-evaluate after 2–3 follow-up interactions';
                    }
                    if (!empty($r['has_previous_software'])) {
                        $actions[] = 'Prepare migration plan from ' . ($r['previous_software_name'] ?? 'current system');
                    }
                    if (!empty($r['follow_up_date'])) {
                        $actions[] = 'Scheduled follow-up: ' . \Carbon\Carbon::parse($r['follow_up_date'])->format('d M Y');
                    }
                @endphp
                @foreach($actions as $a)
                    <li>{{ $a }}</li>
                @endforeach
            </ul>
        </div>
        @endif

    </div>

    {{-- FOOTER --}}
    <div class="rpt-footer">
        <div>
            <strong>{{ $companyName }}</strong> · Lead Contact Report · Confidential<br>
            Report ID: LCR-{{ str_pad($lead->id, 4, '0', STR_PAD_LEFT) }} · Generated: {{ now()->format('d M Y, h:i A') }}
        </div>
        <div class="sig-block">
            <div class="sig-item">
                <div class="sig-line"></div>
                <div class="sig-label">Prepared By</div>
            </div>
            <div class="sig-item">
                <div class="sig-line"></div>
                <div class="sig-label">Reviewed By</div>
            </div>
        </div>
    </div>

</div>
</div>

</body>
</html>
