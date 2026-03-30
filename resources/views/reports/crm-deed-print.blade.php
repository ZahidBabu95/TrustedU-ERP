<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Software Service Agreement — {{ $deal->company ?? $deal->title }}</title>
    <style>
        @page { size: A4; margin: 0; }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #1e293b; background: #f1f5f9; font-size: 12px; line-height: 1.7; }
        .page { width: 210mm; min-height: 297mm; margin: 0 auto; background: #fff; padding: 0; position: relative; }
        @media print { body { background: #fff; } .page { margin: 0; box-shadow: none; break-after: page; } .no-print { display: none !important; } }
        @media screen { .page { box-shadow: 0 4px 30px rgba(0,0,0,.08); margin: 20px auto; } }

        /* ── Header ── */
        .deed-header { background: linear-gradient(135deg, #0c4a6e 0%, #075985 50%, #0c4a6e 100%); color: #fff; padding: 20px 36px; display: flex; justify-content: space-between; align-items: center; border-bottom: 4px solid #f59e0b; }
        .deed-logo h1 { font-size: 22px; font-weight: 800; letter-spacing: 1px; }
        .deed-logo p { font-size: 10px; opacity: .8; margin-top: 2px; }
        .deed-contact { text-align: right; font-size: 10px; line-height: 1.8; }
        .deed-contact a { color: #fbbf24; text-decoration: none; }

        /* ── Title ── */
        .deed-title { text-align: center; padding: 20px 36px 12px; }
        .deed-title h2 { font-size: 18px; font-weight: 800; color: #0c4a6e; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px; }

        /* ── Body ── */
        .deed-body { padding: 0 36px; font-size: 11.5px; }
        .deed-body p { margin-bottom: 8px; text-align: justify; }
        .deed-body strong { color: #0c4a6e; }

        /* ── Section Headings ── */
        .section-title { font-size: 13px; font-weight: 800; color: #0c4a6e; margin: 14px 0 6px; padding: 4px 0; border-bottom: 1px solid #e2e8f0; }
        .sub-section { font-weight: 700; margin: 6px 0 3px; }

        /* ── Lists ── */
        .deed-body ul { margin-left: 24px; margin-bottom: 8px; }
        .deed-body ul li { margin-bottom: 3px; }

        /* ── Signatures ── */
        .deed-signatures { display: flex; justify-content: space-between; padding: 20px 36px; margin-top: 16px; }
        .sig-block { width: 45%; }
        .sig-block h4 { font-size: 11px; font-weight: 800; color: #0c4a6e; margin-bottom: 6px; }
        .sig-block p { font-size: 11px; margin-bottom: 2px; }
        .sig-line { border-top: 1px solid #475569; margin: 28px 0 4px; width: 200px; }
        .sig-label { font-size: 10px; color: #64748b; }

        /* ── Appendix ── */
        .appendix-title { text-align: center; font-size: 14px; font-weight: 800; color: #0c4a6e; margin: 16px 0 10px; border-top: 2px solid #0c4a6e; padding-top: 12px; }

        /* ── Bank Info ── */
        .bank-box { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 8px; padding: 12px 16px; margin: 8px 0; }
        .bank-box h4 { font-size: 11px; font-weight: 700; color: #0c4a6e; margin-bottom: 4px; }
        .bank-box p { font-size: 11px; margin: 1px 0; }

        /* ── Footer ── */
        .deed-footer { background: #f8fafc; border-top: 1px solid #e2e8f0; padding: 8px 36px; text-align: center; font-size: 9px; color: #94a3b8; }

        /* ── Print BTN ── */
        .print-bar { text-align: center; padding: 16px; }
        .print-bar button { padding: 10px 28px; background: #0c4a6e; color: #fff; border: none; border-radius: 8px; font-size: 14px; font-weight: 700; cursor: pointer; font-family: inherit; margin: 0 4px; }
        .print-bar button:hover { background: #075985; }
        .print-bar .btn-close { background: #64748b; }
    </style>
</head>
<body>
    <div class="no-print print-bar">
        <button onclick="window.print()">🖨️ Print / Save as PDF</button>
        <button class="btn-close" onclick="window.close()">✕ Close</button>
    </div>

    {{-- ═══════════════ PAGE 1: Agreement ═══════════════ --}}
    <div class="page">
        <div class="deed-header">
            <div class="deed-logo">
                <h1>{{ $company['name'] ?? 'Amar School' }}</h1>
                <p>{{ $company['tagline'] ?? 'Manage School Easily' }}</p>
            </div>
            <div class="deed-contact">
                📞 {{ $company['phone'] ?? '+88 01793661417' }}<br>
                ✉️ {{ $company['email'] ?? 'hello.amarschool@gmail.com' }}<br>
                🌐 {{ $company['website'] ?? 'www.amarschool.co' }}
            </div>
        </div>

        <div class="deed-title">
            <h2>{{ $deal->deed_duration ?? '2' }}-Year Software Service Agreement</h2>
        </div>

        <div class="deed-body">
            <p><strong>{{ $client->name ?? $deal->company ?? '—' }}</strong> and <strong>{{ $company['name'] ?? 'Amar School Management Software Company' }}</strong></p>

            <p>This Software Service Agreement ("Agreement") is made and entered into on <strong>{{ $deal->deed_effective_date ? \Carbon\Carbon::parse($deal->deed_effective_date)->format('F d, Y') : now()->format('F d, Y') }}</strong> by and between:</p>

            <p><strong>{{ $client->name ?? $deal->company ?? '—' }}</strong> ("Client"),<br>
            educational institution located at <strong>{{ $client->address ?? $deal->deed_client_address ?? '—' }}</strong><br>
            Represented by <strong>{{ $deal->deed_client_representative ?? $client->principal_name ?? '—' }}</strong></p>

            <p><strong>AND</strong></p>

            <p><strong>{{ $company['name'] ?? 'Amar School Management Software Company' }}</strong> ("Provider"),<br>
            a company registered in Bangladesh, located at {{ $company['address'] ?? 'House #192, Road #2, Avenue #3, Mirpur DOHS, Dhaka 1216' }},<br>
            Contact: {{ $company['phone'] ?? '+8801716282884' }}, {{ $company['email'] ?? 'hello.amarschool@gmail.com' }},<br>
            Website: {{ $company['website'] ?? 'www.amarschool.co' }}<br>
            Represented by <strong>{{ $company['ceo_name'] ?? 'Md. Aminul Islam' }}, {{ $company['ceo_title'] ?? 'CEO' }}</strong>.</p>

            <p>Collectively referred to as the "Parties".</p>

            <div class="section-title">1. Recitals</div>
            <p>WHEREAS, the Client operates an educational institution and seeks to digitize and automate its academic, administrative, and financial operations;</p>
            <p>Meanwhile, the provider offers a cloud-based school management software, <strong>{{ $company['product_name'] ?? 'Amar School' }}</strong>, designed to streamline school operations;</p>
            <p>WHEREAS, the Client desires to engage the Provider to provide software services under the <strong>{{ $deal->deed_plan_name ?? 'Starter Plan' }}</strong>, and the Provider agrees to deliver such services under the terms herein;</p>
            <p>NOW, THEREFORE, the Parties agree as follows:</p>

            <div class="section-title">2. Term</div>
            <p class="sub-section">2.1. Duration:</p>
            <p>This Agreement shall commence on the Effective Date and continue for <strong>{{ $deal->deed_duration ?? '2' }} year(s)</strong>, ending on <strong>{{ $deal->deed_end_date ? \Carbon\Carbon::parse($deal->deed_end_date)->format('F d, Y') : now()->addYears(2)->format('F d, Y') }}</strong>, unless terminated earlier per Section 11.</p>
            <p class="sub-section">2.2. Renewal:</p>
            <p>The Agreement may be renewed by mutual written consent, with updated terms, features, and pricing.</p>

            <div class="section-title">3. Scope of Services</div>
            <p class="sub-section">3.1. Software License:</p>
            <p>The Provider grants the Client a non-exclusive, non-transferable, revocable license to use the {{ $company['product_name'] ?? 'Amar School' }} cloud-based software under the <strong>{{ $deal->deed_plan_name ?? 'Starter Plan' }}</strong> (Tk. {{ number_format($deal->deed_per_user_rate ?? 15, 0) }}/month/user) for internal educational purposes.</p>

            @if(!empty($deal->deed_plan_features))
                <p><strong>{{ $deal->deed_plan_name ?? 'Starter Plan' }} Features</strong> (included):</p>
                <ul>
                    @foreach($deal->deed_plan_features as $feature)
                        <li>{{ $feature }}</li>
                    @endforeach
                </ul>
            @else
                <p><strong>Standard Features</strong> (included):</p>
                <ul>
                    <li>Online admission and student information management.</li>
                    <li>Teacher and staff database management.</li>
                    <li>Attendance tracking via mobile app.</li>
                    <li>Student promotion and academic/exam scheduling.</li>
                    <li>Tuition fee management and online payments.</li>
                    <li>Class timetable, notice board, and syllabus management.</li>
                    <li>Reports and analytics (academic, financial, administrative).</li>
                    <li>Communication tools (SMS notifications, portals, apps).</li>
                    <li>Student/guardian/teacher portals (web and iOS/Android apps).</li>
                    <li>Academic card generator.</li>
                </ul>
            @endif

            <p class="sub-section">3.2. Implementation:</p>
            <p>The Provider shall configure the Software to align with the Client's organizational structure, including user roles, academic terms, and operational workflows.</p>

            <p class="sub-section">3.3. Training and Onboarding:</p>
            <ul>
                <li>Initial training sessions (in-person or remote) for administrators, teachers, and staff, including guidebooks and instructional videos.</li>
                <li>Access to a dedicated onboarding team for setup assistance.</li>
            </ul>

            <p class="sub-section">3.4. Support and Maintenance:</p>
            <ul>
                <li>24/7 technical support via phone, email, WhatsApp, Google Meet, or AnyDesk.</li>
                <li>Resolution of critical bugs within 24 hours of reporting.</li>
                <li>Automatic Software updates and security patches at no additional cost.</li>
                <li>System uptime of at least 99.5%.</li>
            </ul>

            <div class="section-title">4. Fees and Payment</div>
            <p class="sub-section">4.1. Subscription Fees:</p>
            <p>Client Monthly Subscription Package (see Appendix A).</p>
            <p class="sub-section">4.2. Payment Schedule:</p>
            <ul>
                <li><strong>Installation Costs:</strong> {{ number_format($deal->deed_installation_cost ?? 4000, 0) }}TK installation or setup fees.</li>
                <li><strong>Late Payments:</strong> Overdue amounts incur a 2% monthly penalty.</li>
                <li><strong>Payment Method:</strong> Payments shall be made via bank transfer, online payment gateway, or other methods specified by the Provider.</li>
                <li><strong>Taxes:</strong> All fees exclude applicable taxes, which the Client shall bear.</li>
                <li><strong>Non-Refundable:</strong> Payments are non-refundable, except in cases of the Provider's material breach.</li>
            </ul>

            <div class="section-title">5. Client Obligations</div>
            <ul>
                <li><strong>Authorized Use:</strong> The Client shall ensure the Software is used only by authorized users for educational purposes.</li>
                <li><strong>Data Accuracy:</strong> The Client shall provide accurate, complete, and up-to-date data for Software implementation.</li>
                <li><strong>Infrastructure:</strong> The Client shall maintain a stable internet connection and compatible devices.</li>
                <li><strong>Training Compliance:</strong> The Client shall ensure staff attend training sessions and adhere to usage guidelines.</li>
                <li><strong>Credential Security:</strong> The Client shall safeguard login credentials and notify the Provider immediately of any unauthorized access.</li>
            </ul>

            <div class="section-title">6. Data Ownership and Privacy</div>
            <ul>
                <li><strong>Ownership:</strong> The Client retains full ownership of all data entered into the Software.</li>
                <li><strong>Confidentiality:</strong> The Provider shall not disclose Client data to third parties without written consent, except as required by law.</li>
                <li><strong>Security:</strong> The Provider shall implement industry-standard encryption, access controls, and firewalls to protect Client data.</li>
                <li><strong>Backups:</strong> The Provider shall perform daily data backups, stored securely for at least 30 days.</li>
                <li><strong>Data Export:</strong> Upon request or termination, the Provider shall provide a full data export in a standard format within 7 business days.</li>
            </ul>

            <div class="section-title">7. Intellectual Property</div>
            <ul>
                <li><strong>Software Ownership:</strong> The Provider retains all intellectual property rights to the Software, including code, design, and documentation.</li>
                <li><strong>Restrictions:</strong> The Client shall not copy, modify, reverse-engineer, decompile, or distribute the Software.</li>
            </ul>

            <div class="section-title">12. Governing Law and Dispute Resolution</div>
            <ul>
                <li><strong>Governing Law:</strong> This Agreement is governed by the laws of the People's Republic of Bangladesh.</li>
                <li><strong>Dispute Resolution:</strong> Disputes shall be resolved through good-faith negotiation. If unresolved within 30 days, disputes shall be submitted to arbitration in Dhaka.</li>
                <li><strong>Jurisdiction:</strong> Any legal action shall be filed exclusively in the Courts of Dhaka.</li>
            </ul>

            <div class="section-title">13. Miscellaneous</div>
            <ul>
                <li><strong>Entire Agreement:</strong> This Agreement constitutes the entire understanding, superseding all prior agreements.</li>
                <li><strong>Amendments:</strong> Modifications must be in writing and signed by both Parties.</li>
                <li><strong>Force Majeure:</strong> Neither party is liable for delays due to events beyond their control.</li>
                <li><strong>Severability:</strong> If any provision is invalid, the remaining provisions remain enforceable.</li>
            </ul>
        </div>

        {{-- Signatures --}}
        <div class="deed-signatures">
            <div class="sig-block">
                <h4>{{ $client->name ?? $deal->company ?? '—' }}</h4>
                <p>Name: <strong>{{ $deal->deed_client_representative ?? $client->principal_name ?? '—' }}</strong></p>
                <p>Designation: <strong>{{ $deal->deed_client_designation ?? 'Principal' }}</strong></p>
                <p>Email: {{ $client->email ?? '—' }}</p>
                <p>Mobile: {{ $client->phone ?? '—' }}</p>
                <div class="sig-line"></div>
                <p class="sig-label">Signature: ___________________</p>
                <p class="sig-label">Date: ___________________</p>
            </div>
            <div class="sig-block">
                <h4>For {{ $company['name'] ?? 'Amar School Management Software Company' }}</h4>
                <p>Name: <strong>{{ $company['ceo_name'] ?? 'Md. Aminul Islam' }}</strong></p>
                <p>Designation: <strong>{{ $company['ceo_title'] ?? 'CEO' }}</strong></p>
                <div class="sig-line"></div>
                <p class="sig-label">Signature: ___________________</p>
                <p class="sig-label">Date: ___________________</p>
            </div>
        </div>

        <div class="deed-footer">
            <p>{{ $company['name'] ?? 'Amar School Management Software Company' }} · Software Service Agreement · Page 1</p>
        </div>
    </div>

    {{-- ═══════════════ PAGE 2: Appendix A ═══════════════ --}}
    <div class="page">
        <div class="deed-header">
            <div class="deed-logo">
                <h1>{{ $company['name'] ?? 'Amar School' }}</h1>
                <p>{{ $company['tagline'] ?? 'Manage School Easily' }}</p>
            </div>
            <div class="deed-contact">
                📞 {{ $company['phone'] ?? '+88 01793661417' }}<br>
                ✉️ {{ $company['email'] ?? 'hello.amarschool@gmail.com' }}<br>
                🌐 {{ $company['website'] ?? 'www.amarschool.co' }}
            </div>
        </div>

        <div class="deed-body" style="padding-top:16px">
            <div class="appendix-title">Appendix A: Pricing and Payment Schedule</div>

            <ul>
                <li><strong>Plan:</strong> {{ $deal->deed_plan_name ?? 'Economy Plan' }}</li>
                <li><strong>Users:</strong> {{ $deal->deed_total_users ?? '—' }}</li>
                <li><strong>Monthly Fee:</strong> ৳{{ number_format($deal->deed_monthly_fee ?? $deal->value ?? 0, 0) }}</li>
            </ul>

            <ul style="margin-top:12px">
                <li><strong>Installation Costs:</strong> {{ number_format($deal->deed_installation_cost ?? 4000, 0) }}TK installation or setup fees.</li>
                <li><strong>Late Payments:</strong> Overdue amounts incur a 2% monthly penalty.</li>
            </ul>

            <p style="margin-top:12px"><strong>Payment Method:</strong> Bank transfer to</p>

            @if(!empty($deal->deed_bank_accounts))
                @foreach($deal->deed_bank_accounts as $bank)
                    <div class="bank-box">
                        <h4>{{ $bank['bank_name'] ?? '—' }}</h4>
                        <p><strong>A/C Name:</strong> {{ $bank['account_name'] ?? '—' }}</p>
                        <p><strong>A/C No:</strong> {{ $bank['account_number'] ?? '—' }}</p>
                        <p><strong>Branch:</strong> {{ $bank['branch'] ?? '—' }}</p>
                    </div>
                @endforeach
            @else
                <div class="bank-box">
                    <h4>Dutch Bangla Bank Limited</h4>
                    <p><strong>A/C Name:</strong> MD. AMINUL HAQUE</p>
                    <p><strong>A/C No:</strong> 114.103.209253</p>
                    <p><strong>Branch:</strong> Mohakhali Branch</p>
                </div>
                <div class="bank-box">
                    <h4>City Bank Limited</h4>
                    <p><strong>A/C Name:</strong> Amar Uddog Limited</p>
                    <p><strong>A/C No:</strong> 1421941455001</p>
                    <p><strong>Branch:</strong> Bonani Branch</p>
                </div>
            @endif

            <ul style="margin-top:12px">
                <li><strong>Optional Services:</strong> SMS notifications, custom reports, or additional features at quoted rates (contact {{ $company['phone'] ?? '+8801790665149' }}).</li>
            </ul>

            {{-- Signatures for Appendix --}}
            <div style="margin-top:40px" class="deed-signatures">
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <p class="sig-label">Client Signature</p>
                    <p class="sig-label">Date: ___________________</p>
                </div>
                <div class="sig-block">
                    <div class="sig-line"></div>
                    <p class="sig-label">Provider Signature</p>
                    <p class="sig-label">Date: ___________________</p>
                </div>
            </div>
        </div>

        <div class="deed-footer" style="position:absolute;bottom:0;left:0;right:0">
            <p>{{ $company['name'] ?? 'Amar School Management Software Company' }} · Appendix A: Pricing and Payment Schedule · Page 2</p>
        </div>
    </div>
</body>
</html>
