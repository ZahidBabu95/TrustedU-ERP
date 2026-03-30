<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page { size: A4; margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; background: #f1f5f9; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; padding: 0; position: relative; }
        @media print {
            body { background: #fff; }
            .page { margin: 0; box-shadow: none; }
            .no-print { display: none !important; }
        }
        @media screen {
            .page { box-shadow: 0 4px 30px rgba(0,0,0,.08); margin: 20px auto; }
        }

        /* ── Header ── */
        .inv-header { background: linear-gradient(135deg, #1e3a5f 0%, #2d5a87 50%, #1e3a5f 100%); color: #fff; padding: 28px 36px 22px; display: flex; justify-content: space-between; align-items: flex-start; }
        .inv-company h1 { font-size: 22px; font-weight: 800; letter-spacing: .5px; margin-bottom: 4px; }
        .inv-company p { font-size: 11px; opacity: .85; line-height: 1.6; }
        .inv-badge { text-align: right; }
        .inv-badge h2 { font-size: 28px; font-weight: 900; letter-spacing: 2px; text-transform: uppercase; margin-bottom: 4px; }
        .inv-badge .inv-num { font-size: 13px; background: rgba(255,255,255,.2); padding: 4px 12px; border-radius: 16px; display: inline-block; }

        /* ── Status Badge ── */
        .inv-status { position: absolute; top: 90px; right: 36px; font-size: 11px; font-weight: 700; padding: 4px 14px; border-radius: 16px; text-transform: uppercase; letter-spacing: 1px; }
        .inv-status--draft { background: #f1f5f9; color: #64748b; }
        .inv-status--sent { background: #dbeafe; color: #2563eb; }
        .inv-status--paid { background: #dcfce7; color: #16a34a; }
        .inv-status--partially_paid { background: #fef3c7; color: #d97706; }
        .inv-status--overdue { background: #fef2f2; color: #dc2626; }

        /* ── Info Grid ── */
        .inv-info { display: flex; justify-content: space-between; padding: 20px 36px 16px; border-bottom: 1px solid #e2e8f0; }
        .inv-info-block { flex: 1; }
        .inv-info-block h3 { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1.5px; color: #94a3b8; margin-bottom: 6px; }
        .inv-info-block p { font-size: 12px; line-height: 1.7; color: #334155; }
        .inv-info-block p strong { color: #1e293b; font-weight: 700; }

        /* ── Dates ── */
        .inv-dates { display: flex; gap: 0; padding: 0 36px; margin: 12px 0; }
        .inv-date-box { flex: 1; padding: 10px 14px; background: #f8fafc; border: 1px solid #e2e8f0; }
        .inv-date-box:first-child { border-radius: 8px 0 0 8px; }
        .inv-date-box:last-child { border-radius: 0 8px 8px 0; }
        .inv-date-box label { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; display: block; }
        .inv-date-box span { font-size: 13px; font-weight: 700; color: #1e293b; }

        /* ── Items Table ── */
        .inv-table-wrap { padding: 0 36px; margin-top: 16px; }
        table { width: 100%; border-collapse: collapse; }
        thead th { background: #1e3a5f; color: #fff; padding: 10px 12px; font-size: 10px; text-transform: uppercase; letter-spacing: 1px; font-weight: 700; text-align: left; }
        thead th:first-child { border-radius: 6px 0 0 0; }
        thead th:last-child { border-radius: 0 6px 0 0; text-align: right; }
        thead th.center { text-align: center; }
        thead th.right { text-align: right; }
        tbody td { padding: 10px 12px; font-size: 12px; border-bottom: 1px solid #f1f5f9; color: #334155; }
        tbody td.center { text-align: center; }
        tbody td.right { text-align: right; font-weight: 600; }
        tbody tr:nth-child(even) { background: #fafbfc; }
        tbody tr:hover { background: #f1f5f9; }

        /* ── Totals ── */
        .inv-totals { display: flex; justify-content: flex-end; padding: 0 36px; margin-top: 8px; }
        .inv-totals-table { width: 260px; }
        .inv-total-row { display: flex; justify-content: space-between; padding: 6px 0; font-size: 12px; color: #475569; border-bottom: 1px solid #f1f5f9; }
        .inv-total-row.grand { font-size: 16px; font-weight: 800; color: #1e3a5f; border-top: 2px solid #1e3a5f; border-bottom: 2px solid #1e3a5f; padding: 10px 0; margin-top: 4px; }
        .inv-total-row.paid { color: #16a34a; font-weight: 600; }
        .inv-total-row.due { color: #dc2626; font-weight: 700; font-size: 14px; }

        /* ── Amount in Words ── */
        .inv-words { padding: 12px 36px 0; }
        .inv-words p { font-size: 11px; color: #64748b; font-style: italic; background: #f8fafc; padding: 8px 14px; border-radius: 6px; border-left: 3px solid #1e3a5f; }

        /* ── Payment Info ── */
        .inv-payment { padding: 12px 36px; margin-top: 8px; }
        .inv-payment h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; margin-bottom: 6px; }
        .inv-payment p { font-size: 11px; color: #475569; line-height: 1.6; }

        /* ── Terms ── */
        .inv-terms { padding: 12px 36px; margin-top: 6px; }
        .inv-terms h3 { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; font-weight: 700; margin-bottom: 6px; }
        .inv-terms p { font-size: 10px; color: #94a3b8; line-height: 1.7; white-space: pre-line; }

        /* ── Signatures ── */
        .inv-signatures { display: flex; justify-content: space-between; padding: 32px 36px 16px; margin-top: 20px; }
        .inv-sig-block { text-align: center; width: 200px; }
        .inv-sig-line { border-top: 1px solid #cbd5e1; padding-top: 6px; margin-top: 40px; font-size: 10px; color: #64748b; }

        /* ── Footer ── */
        .inv-footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 12px 36px; text-align: center; position: absolute; bottom: 0; left: 0; right: 0; }
        .inv-footer p { font-size: 9px; color: #94a3b8; }

        /* ── Print Button ── */
        .print-bar { text-align: center; padding: 16px; }
        .print-bar button { padding: 10px 28px; background: #1e3a5f; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; }
        .print-bar button:hover { background: #2d5a87; }
    </style>
</head>
<body>
    <div class="no-print print-bar">
        <button onclick="window.print()">🖨️ Print Invoice</button>
        <button onclick="window.close()" style="background:#64748b;margin-left:8px">✕ Close</button>
    </div>

    <div class="page">
        {{-- Header --}}
        <div class="inv-header">
            <div class="inv-company">
                <h1>{{ $invoice->company_name ?? 'TrustedU Technologies' }}</h1>
                <p>
                    {{ $invoice->company_address ?? 'Dhaka, Bangladesh' }}<br>
                    📞 {{ $invoice->company_phone ?? '' }} · ✉️ {{ $invoice->company_email ?? '' }}
                </p>
            </div>
            <div class="inv-badge">
                <h2>Invoice</h2>
                <span class="inv-num">{{ $invoice->invoice_number }}</span>
            </div>
        </div>

        {{-- Info --}}
        <div class="inv-info">
            <div class="inv-info-block">
                <h3>Bill To</h3>
                <p>
                    <strong>{{ $invoice->client_name ?? $invoice->client?->name ?? '—' }}</strong><br>
                    {{ $invoice->client_address ?? $invoice->client?->address ?? '' }}<br>
                    📞 {{ $invoice->client_phone ?? $invoice->client?->phone ?? '' }}<br>
                    ✉️ {{ $invoice->client_email ?? $invoice->client?->email ?? '' }}
                </p>
            </div>
            <div class="inv-info-block" style="text-align:right">
                <h3>Invoice Info</h3>
                <p>
                    <strong>Title:</strong> {{ $invoice->title }}<br>
                    @if($invoice->client?->client_id)<strong>Client ID:</strong> {{ $invoice->client->client_id }}<br>@endif
                    <strong>Created By:</strong> {{ $invoice->creator?->name ?? '—' }}
                </p>
            </div>
        </div>

        {{-- Dates --}}
        <div class="inv-dates">
            <div class="inv-date-box">
                <label>Issue Date</label>
                <span>{{ $invoice->issue_date?->format('d M Y') ?? '—' }}</span>
            </div>
            <div class="inv-date-box">
                <label>Due Date</label>
                <span>{{ $invoice->due_date?->format('d M Y') ?? '—' }}</span>
            </div>
            <div class="inv-date-box">
                <label>Status</label>
                <span>{{ \App\Models\CrmInvoice::STATUS_LABELS[$invoice->status] ?? $invoice->status }}</span>
            </div>
            <div class="inv-date-box">
                <label>Currency</label>
                <span>{{ $invoice->currency ?? 'BDT' }} (৳)</span>
            </div>
        </div>

        {{-- Items Table --}}
        <div class="inv-table-wrap">
            <table>
                <thead>
                    <tr>
                        <th style="width:40px">#</th>
                        <th>Description</th>
                        <th class="center" style="width:60px">Qty</th>
                        <th class="right" style="width:100px">Rate</th>
                        <th class="right" style="width:110px">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @php $items = $invoice->items ?? []; @endphp
                    @forelse($items as $i => $item)
                        @php
                            $qty = $item['qty'] ?? 1;
                            $rate = $item['rate'] ?? $item['amount'] ?? 0;
                            $amount = $qty * $rate;
                        @endphp
                        <tr>
                            <td>{{ $i + 1 }}</td>
                            <td>{{ $item['description'] ?? '—' }}</td>
                            <td class="center">{{ $qty }}</td>
                            <td class="right">৳{{ number_format($rate, 2) }}</td>
                            <td class="right">৳{{ number_format($amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="text-align:center;color:#94a3b8;padding:20px">No items</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Totals --}}
        <div class="inv-totals">
            <div class="inv-totals-table">
                <div class="inv-total-row"><span>Subtotal</span><span>৳{{ number_format($invoice->subtotal, 2) }}</span></div>
                @if($invoice->tax_percent > 0)
                    <div class="inv-total-row"><span>Tax ({{ $invoice->tax_percent }}%)</span><span>৳{{ number_format($invoice->tax_amount, 2) }}</span></div>
                @endif
                @if($invoice->discount_amount > 0)
                    <div class="inv-total-row"><span>Discount</span><span style="color:#dc2626">-৳{{ number_format($invoice->discount_amount, 2) }}</span></div>
                @endif
                <div class="inv-total-row grand"><span>Total</span><span>৳{{ number_format($invoice->total, 2) }}</span></div>
                @if($invoice->paid_amount > 0)
                    <div class="inv-total-row paid"><span>Paid</span><span>৳{{ number_format($invoice->paid_amount, 2) }}</span></div>
                @endif
                @if($invoice->due_amount > 0)
                    <div class="inv-total-row due"><span>Balance Due</span><span>৳{{ number_format($invoice->due_amount, 2) }}</span></div>
                @endif
            </div>
        </div>

        {{-- Amount in Words --}}
        <div class="inv-words">
            <p><strong>In Words:</strong> {{ $invoice->amount_in_words }}</p>
        </div>

        {{-- Payment Method --}}
        @if($invoice->payment_method)
            <div class="inv-payment">
                <h3>Payment Method</h3>
                <p>{{ \App\Models\CrmInvoice::PAYMENT_METHOD_LABELS[$invoice->payment_method] ?? $invoice->payment_method }}</p>
            </div>
        @endif

        {{-- Terms --}}
        @if($invoice->terms_conditions)
            <div class="inv-terms">
                <h3>Terms & Conditions</h3>
                <p>{{ $invoice->terms_conditions }}</p>
            </div>
        @endif

        {{-- Signatures --}}
        <div class="inv-signatures">
            <div class="inv-sig-block">
                <div class="inv-sig-line">Authorized Signature</div>
            </div>
            <div class="inv-sig-block">
                <div class="inv-sig-line">Client Signature</div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="inv-footer">
            <p>{{ $invoice->company_name ?? 'TrustedU Technologies' }} · This is a computer-generated invoice · Printed on {{ now()->format('d M Y, h:i A') }}</p>
        </div>
    </div>
</body>
</html>
