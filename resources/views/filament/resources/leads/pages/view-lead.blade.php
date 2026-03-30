<x-filament-panels::page>
@php
    $lead = $this->record;
    $steps = $this->getSteps();
    $currentStatus = $lead->status;
    $currentIndex = collect($steps)->search(fn($s) => $s['key'] === $currentStatus);
    if ($currentIndex === false) $currentIndex = -1;
    $isLost = $currentStatus === 'lost';
    $report = $lead->contact_report ?? [];
    $erpModules = $this->getErpModules();
@endphp

<div class="pl" x-data="{ showLostForm: false }">
<div class="pl-inner">

    {{-- HEADER --}}
    <div class="pl-head">
        <div class="pl-head-info">
            <div class="pl-avatar">{{ strtoupper(mb_substr($lead->name, 0, 2)) }}</div>
            <div class="pl-meta">
                <h2 class="pl-name">{{ $lead->name }}</h2>
                <p class="pl-detail">{{ collect([$lead->email, $lead->phone, \App\Models\Lead::SOURCE_LABELS[$lead->source] ?? null])->filter()->implode(' · ') }}</p>
            </div>
        </div>
        <div class="pl-head-actions">
            @if($lead->interest_level)
                <span class="pl-pill pl-pill--{{ $lead->interest_level }}">
                    {{ $lead->interest_level === 'hot' ? '🔥 Hot' : ($lead->interest_level === 'warm' ? '🌤 Warm' : '❄️ Cold') }}
                </span>
            @endif
            @if($lead->value)<span class="pl-pill pl-pill--val">৳{{ number_format($lead->value) }}</span>@endif
            @if(!empty($report) && $currentStatus !== 'new')
                <a href="{{ route('lead.contact-report', $lead) }}" target="_blank" class="pl-btn-sm pl-btn-sm--accent">📄 Report</a>
            @endif
            <a href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('edit', ['record' => $lead]) }}" class="pl-btn-sm">✏️ Edit</a>
            <a href="{{ \App\Filament\Resources\Leads\LeadResource::getUrl('index') }}" class="pl-btn-sm pl-btn-sm--ghost">← Back</a>
        </div>
    </div>

    @if($isLost)
        <div class="pl-banner pl-banner--lost">❌ <strong>Lead Lost</strong> @if($lead->lost_reason)— {{ $lead->lost_reason }}@endif</div>
    @endif

    {{-- STEPPER --}}
    <div class="pl-steps">
        @foreach($steps as $i => $step)
            @php $done=$i<$currentIndex; $cur=$i===$currentIndex; @endphp
            <div class="pl-step {{ $done?'pl-step--done':'' }} {{ $cur?'pl-step--cur':'' }} {{ $done?'pl-step--clickable':'' }}"
                 @if($done) wire:click="viewStep('{{ $step['key'] }}')" title="Click to view {{ $step['label'] }}" @endif>
                <div class="pl-step__dot">
                    @if($done)<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="3"><path d="M5 13l4 4L19 7"/></svg>
                    @elseif($cur)<span>{{ $step['icon'] }}</span>
                    @else<span class="pl-step__num">{{ $i+1 }}</span>@endif
                </div>
                <span class="pl-step__label">{{ $step['label'] }}</span>
            </div>
            @if(!$loop->last)<div class="pl-step__line {{ $done?'pl-step__line--done':'' }} {{ $cur?'pl-step__line--cur':'' }}"></div>@endif
        @endforeach
    </div>

    {{-- STATUS BAR --}}
    <div class="pl-status">
        <span class="pl-status__current"><span class="pl-status__dot pl-status__dot--{{ $currentStatus }}"></span><strong>{{ \App\Models\Lead::STATUS_LABELS[$currentStatus] ?? ucfirst($currentStatus) }}</strong></span>
        @if(!$isLost && $currentIndex < count($steps) - 1)
            <span class="pl-status__next">→ Next: {{ $steps[$currentIndex + 1]['label'] ?? '' }}</span>
        @elseif($currentStatus === 'won')
            <span class="pl-status__won">🎉 Complete!</span>
        @endif
        @if($lead->status_changed_at)<span class="pl-status__time">{{ $lead->status_changed_at->diffForHumans() }}</span>@endif
    </div>

    {{-- ═══ VIEWING PREVIOUS STEP (Read-Only Panel) ═══ --}}
    @if($this->viewingStep)
        @php $vs = $this->viewingStep; @endphp
        <div class="pl-card" style="border:2px solid #3b82f6;background:#eff6ff">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
                <div class="pl-card__head" style="margin:0">👁️ Viewing: {{ \App\Models\Lead::STATUS_LABELS[$vs] ?? ucfirst($vs) }} (Read-Only)</div>
                <div style="display:flex;gap:6px">
                    <button wire:click="clearViewStep" class="pl-btn-sm" style="background:#fff">← Back to Current Step</button>
                    <button wire:click="editStep('{{ $vs }}')" class="pl-btn-sm pl-btn-sm--accent" wire:confirm="This will revert the status to {{ \App\Models\Lead::STATUS_LABELS[$vs] ?? $vs }} for editing. Continue?">✏️ Edit This Step</button>
                </div>
            </div>

            {{-- VIEW: Contact Report --}}
            @if($vs === 'new' || $vs === 'contacted')
                @php $vr = $lead->contact_report ?? []; @endphp
                <div class="pl-data-grid">
                    <div class="pl-data"><label>Institution</label><span>{{ $vr['institution_name'] ?? '—' }}</span></div>
                    <div class="pl-data"><label>Address</label><span>{{ $vr['institution_address'] ?? '—' }}</span></div>
                    <div class="pl-data"><label>Students</label><span>{{ $vr['student_count'] ?? '—' }}</span></div>
                    <div class="pl-data"><label>Decision Maker</label><span>{{ $vr['decision_maker_name'] ?? '—' }} {{ isset($vr['decision_maker_designation']) ? '('.$vr['decision_maker_designation'].')' : '' }}</span></div>
                    <div class="pl-data"><label>DM Phone</label><span>{{ $vr['decision_maker_phone'] ?? '—' }}</span></div>
                    <div class="pl-data"><label>Contact Date</label><span>{{ $vr['contacted_at'] ?? '—' }}</span></div>
                    <div class="pl-data"><label>Budget</label><span>{{ $vr['budget_range'] ?? '—' }}</span></div>
                    <div class="pl-data"><label>Assessment</label><span>{{ $vr['interest_assessment'] ?? '—' }}</span></div>
                </div>
                @if(!empty($vr['conversation_summary']))
                    <div class="pl-data-block"><label>Conversation Summary</label><p>{{ $vr['conversation_summary'] }}</p></div>
                @endif
                @if(!empty($vr['desired_modules']))
                    <div class="pl-data-block"><label>Desired Modules</label>
                        <div class="pl-chip-list">@foreach($vr['desired_modules'] as $m)<span class="pl-chip">{{ $erpModules[$m] ?? $m }}</span>@endforeach</div>
                    </div>
                @endif
                @if(!empty($vr['conversion_probability']))
                    <div class="pl-data-block pl-data-block--accent"><label>Conversion Probability</label>
                        <div class="pl-progress"><div class="pl-progress__bar" style="width:{{ $vr['conversion_probability'] }}%">{{ $vr['conversion_probability'] }}%</div></div>
                    </div>
                @endif
            @endif

            {{-- VIEW: Qualified (just shows it was approved) --}}
            @if($vs === 'qualified')
                <div class="pl-data-grid">
                    <div class="pl-data"><label>Status</label><span style="color:#059669;font-weight:700">✅ Qualified</span></div>
                    <div class="pl-data"><label>Note</label><span>Lead was qualified for proposal creation.</span></div>
                </div>
            @endif

            {{-- VIEW: Proposal --}}
            @if($vs === 'proposal')
                @php $vpd = $lead->proposal_data ?? []; $vmods = $vpd['modules'] ?? []; $vbase = (float)($vpd['base_price'] ?? 0); $vdisc = (float)($vpd['discount_percent'] ?? 0); $vfinal = max(0, $vbase - ($vbase * $vdisc / 100)); @endphp
                <div class="pl-data-grid">
                    <div class="pl-data"><label>Title</label><span>{{ $vpd['title'] ?? '—' }}</span></div>
                    <div class="pl-data"><label>Base Price</label><span>৳{{ number_format($vbase) }}</span></div>
                    <div class="pl-data"><label>Discount</label><span>{{ $vdisc }}%</span></div>
                    <div class="pl-data"><label>Final Price</label><span style="color:#059669;font-size:16px;font-weight:700">৳{{ number_format($vfinal) }}</span></div>
                    <div class="pl-data"><label>Implementation</label><span>{{ $vpd['implementation_days'] ?? '—' }} days</span></div>
                    <div class="pl-data"><label>Validity</label><span>{{ $vpd['validity_days'] ?? '—' }} days</span></div>
                </div>
                @if(!empty($vmods))
                    <div class="pl-data-block"><label>Selected Modules</label>
                        <div class="pl-chip-list">@foreach($vmods as $m)<span class="pl-chip">{{ $erpModules[$m] ?? $m }}</span>@endforeach</div>
                    </div>
                @endif
                @if(!empty($vpd['payment_terms']))<div class="pl-data-block"><label>Payment Terms</label><p>{{ $vpd['payment_terms'] }}</p></div>@endif
                <div style="margin-top:8px">
                    <a href="{{ route('lead.proposal-report', $lead) }}" target="_blank" class="pl-btn-sm pl-btn-sm--accent">📄 Download Proposal</a>
                </div>
            @endif

            {{-- VIEW: Negotiation --}}
            @if($vs === 'negotiation')
                @php $vlogs = $lead->negotiationLogs()->with('logger')->get(); @endphp
                @if($vlogs->count() > 0)
                    @foreach($vlogs as $log)
                        <div style="padding:8px 12px;background:#fff;border-radius:8px;margin-bottom:6px;border-left:3px solid {{ $log->client_response === 'positive' ? '#22c55e' : ($log->client_response === 'negative' ? '#ef4444' : '#f59e0b') }}">
                            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:3px">
                                <span style="font-weight:700;font-size:12px">{{ \App\Models\NegotiationLog::TYPE_LABELS[$log->discussion_type] ?? $log->discussion_type }}</span>
                                <span style="font-size:11px;color:#9ca3af">{{ $log->discussion_date->format('d M Y, h:i A') }}</span>
                            </div>
                            <p style="margin:2px 0;font-size:12px;color:#6b7280">{{ $log->summary }}</p>
                            <div style="display:flex;gap:10px;flex-wrap:wrap;margin-top:4px;font-size:11px;color:#9ca3af">
                                @if($log->counter_offer)<span>💰 ৳{{ number_format($log->counter_offer) }}</span>@endif
                                @if($log->client_response)<span>{{ \App\Models\NegotiationLog::RESPONSE_LABELS[$log->client_response] ?? '' }}</span>@endif
                                <span>by {{ $log->logger?->name }}</span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <p style="font-size:12px;color:#9ca3af">No negotiation logs recorded.</p>
                @endif
            @endif
        </div>
    @endif

    @if(!$this->viewingStep)
    @if(!empty($report) && $currentStatus !== 'new')
        <div class="pl-card">
            <div class="pl-card__head">📋 Contact Report</div>
            <div class="pl-data-grid">
                <div class="pl-data"><label>Institution</label><span>{{ $report['institution_name'] ?? '—' }}</span></div>
                <div class="pl-data"><label>Address</label><span>{{ $report['institution_address'] ?? '—' }}</span></div>
                <div class="pl-data"><label>Students</label><span>{{ $report['student_count'] ?? '—' }}</span></div>
                <div class="pl-data"><label>Decision Maker</label><span>{{ $report['decision_maker_name'] ?? '—' }} {{ isset($report['decision_maker_designation']) ? '('.$report['decision_maker_designation'].')' : '' }}</span></div>
                <div class="pl-data"><label>DM Phone</label><span>{{ $report['decision_maker_phone'] ?? '—' }}</span></div>
                <div class="pl-data"><label>Contact Date</label><span>{{ $report['contacted_at'] ?? '—' }}</span></div>
                <div class="pl-data"><label>Budget</label><span>{{ $report['budget_range'] ?? '—' }}</span></div>
                <div class="pl-data"><label>Assessment</label><span>{{ $report['interest_assessment'] ?? '—' }}</span></div>
            </div>
            @if(!empty($report['conversation_summary']))
                <div class="pl-data-block"><label>Conversation Summary</label><p>{{ $report['conversation_summary'] }}</p></div>
            @endif
            @if(!empty($report['has_previous_software']))
                <div class="pl-data-block"><label>Previous Software</label><p><strong>{{ $report['previous_software_name'] ?? '—' }}</strong> @if(!empty($report['switch_reason']))— {{ $report['switch_reason'] }}@endif</p></div>
            @endif
            @if(!empty($report['desired_modules']))
                <div class="pl-data-block"><label>Desired Modules</label>
                    <div class="pl-chip-list">@foreach($report['desired_modules'] as $m)<span class="pl-chip">{{ $erpModules[$m] ?? $m }}</span>@endforeach</div>
                </div>
            @endif
            @if(!empty($report['primary_needs']))
                <div class="pl-data-block"><label>Primary Needs</label><p>{{ $report['primary_needs'] }}</p></div>
            @endif
            @if(!empty($report['conversion_probability']))
                <div class="pl-data-block pl-data-block--accent">
                    <label>Conversion Probability</label>
                    <div class="pl-progress"><div class="pl-progress__bar" style="width:{{ $report['conversion_probability'] }}%">{{ $report['conversion_probability'] }}%</div></div>
                    @if(!empty($report['conversion_comment']))<p class="pl-progress__note">{{ $report['conversion_comment'] }}</p>@endif
                </div>
            @endif
        </div>
    @endif

    {{-- CONTACT REPORT FORM --}}
    @if($currentStatus === 'new')
        <div class="pl-card">
            <div class="pl-card__head">📞 Contact Report</div>
            <p class="pl-card__sub">Complete this report to advance to <strong>Contacted</strong>.</p>

            <form wire:submit.prevent="advanceToContacted" class="pl-form">

                <fieldset class="pl-fieldset">
                    <legend>🏫 Institution Details</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group">
                            <label>Institution Name <abbr>*</abbr></label>
                            <input type="text" wire:model="contactData.institution_name" required placeholder="Enter institution name">
                        </div>
                        <div class="pl-input-group">
                            <label>Type</label>
                            <select wire:model="contactData.institution_type">
                                <option value="">— Select —</option>
                                @foreach(\App\Models\Lead::INSTITUTE_TYPE_LABELS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select>
                        </div>
                    </div>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group">
                            <label>Address</label>
                            <input type="text" wire:model="contactData.institution_address" placeholder="Full address">
                        </div>
                        <div class="pl-input-group pl-input-group--narrow">
                            <label>No. of Students</label>
                            <input type="number" wire:model="contactData.student_count" placeholder="Approx.">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="pl-fieldset">
                    <legend>👤 Decision Maker</legend>
                    <div class="pl-grid pl-grid--3">
                        <div class="pl-input-group">
                            <label>Name <abbr>*</abbr></label>
                            <input type="text" wire:model="contactData.decision_maker_name" required placeholder="Decision maker name">
                        </div>
                        <div class="pl-input-group">
                            <label>Designation</label>
                            <input type="text" wire:model="contactData.decision_maker_designation" placeholder="e.g. Principal">
                        </div>
                        <div class="pl-input-group">
                            <label>Phone</label>
                            <input type="text" wire:model="contactData.decision_maker_phone" placeholder="01XXXXXXXXX">
                        </div>
                    </div>
                </fieldset>

                <fieldset class="pl-fieldset">
                    <legend>💬 Conversation Details</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group">
                            <label>Contacted Person</label>
                            <input type="text" wire:model="contactData.contacted_person" placeholder="Person spoken with">
                        </div>
                        <div class="pl-input-group">
                            <label>Contact Date & Time</label>
                            <input type="datetime-local" wire:model="contactData.contacted_at">
                        </div>
                    </div>
                    <div class="pl-input-group">
                        <label>Conversation Summary <abbr>*</abbr></label>
                        <textarea wire:model="contactData.conversation_summary" rows="3" required placeholder="Key discussion points, their needs, concerns, and interests..."></textarea>
                    </div>
                </fieldset>

                <fieldset class="pl-fieldset" x-data="{ hasPrev: @entangle('contactData.has_previous_software') }">
                    <legend>🖥️ Previous Software</legend>
                    <label class="pl-toggle">
                        <input type="checkbox" x-model="hasPrev" wire:model.live="contactData.has_previous_software">
                        <span class="pl-toggle__track"><span class="pl-toggle__thumb"></span></span>
                        <span class="pl-toggle__text">Used software before</span>
                    </label>
                    <div x-show="hasPrev" x-collapse>
                        <div class="pl-grid pl-grid--2" style="margin-top:10px; margin-bottom:0;">
                            <div class="pl-input-group">
                                <label>Software Name</label>
                                <input type="text" wire:model="contactData.previous_software_name" placeholder="Which software?">
                            </div>
                            <div class="pl-input-group">
                                <label>Reason for Switch</label>
                                <input type="text" wire:model="contactData.switch_reason" placeholder="Why switching to us?">
                            </div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="pl-fieldset">
                    <legend>📦 Requirements & Budget</legend>
                    <div class="pl-input-group">
                        <label>Which modules do they need?</label>
                        <div class="pl-module-grid">
                            @foreach($erpModules as $slug => $name)
                                <label class="pl-module-item">
                                    <input type="checkbox" wire:model="contactData.desired_modules" value="{{ $slug }}">
                                    <span class="pl-module-item__box"></span>
                                    <span class="pl-module-item__text">{{ $name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group">
                            <label>Monthly Budget Range</label>
                            <select wire:model="contactData.budget_range">
                                <option value="">— Select —</option>
                                <option value="under_3000">Under ৳3,000</option>
                                <option value="3000_5000">৳3,000 – ৳5,000</option>
                                <option value="5000_10000">৳5,000 – ৳10,000</option>
                                <option value="10000_20000">৳10,000 – ৳20,000</option>
                                <option value="20000_50000">৳20,000 – ৳50,000</option>
                                <option value="above_50000">৳50,000+</option>
                            </select>
                        </div>
                        <div class="pl-input-group">
                            <label>Interest Level</label>
                            <select wire:model="contactData.interest_assessment">
                                <option value="">— Select —</option>
                                <option value="very_interested">Very Interested</option>
                                <option value="somewhat_interested">Somewhat Interested</option>
                                <option value="just_exploring">Just Exploring</option>
                                <option value="not_interested">Not Interested</option>
                            </select>
                        </div>
                    </div>
                    <div class="pl-input-group">
                        <label>Primary Needs</label>
                        <textarea wire:model="contactData.primary_needs" rows="2" placeholder="Their main pain points and what they want to solve..."></textarea>
                    </div>
                </fieldset>

                <fieldset class="pl-fieldset pl-fieldset--accent">
                    <legend>📊 Conversion Assessment</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group pl-input-group--narrow">
                            <label>Probability (%)</label>
                            <div class="pl-range-wrap">
                                <input type="number" wire:model="contactData.conversion_probability" min="0" max="100" placeholder="0–100" class="pl-range-input">
                                <span class="pl-range-unit">%</span>
                            </div>
                        </div>
                        <div class="pl-input-group">
                            <label>Your Comment / Reasoning</label>
                            <textarea wire:model="contactData.conversion_comment" rows="2" placeholder="Why will/won't this lead convert? Key signals?"></textarea>
                        </div>
                    </div>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group">
                            <label>Follow-up Date</label>
                            <input type="date" wire:model="contactData.follow_up_date">
                        </div>
                        <div class="pl-input-group">
                            <label>Additional Notes</label>
                            <input type="text" wire:model="contactData.additional_notes" placeholder="Any extra info...">
                        </div>
                    </div>
                </fieldset>

                <div class="pl-form-footer">
                    <button type="submit" class="pl-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="advanceToContacted">📞 Save Report & Advance to Contacted</span>
                        <span wire:loading wire:target="advanceToContacted">⏳ Saving...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- QUALIFICATION --}}
    @if($currentStatus === 'contacted')
        <div class="pl-card pl-card--dashed">
            <div class="pl-card__head">⏳ Awaiting Qualification Approval</div>
            <p class="pl-card__sub">Review the Contact Report above. Once qualified, Proposal creation will begin.</p>
            <div class="pl-form-footer">
                <button wire:click="qualifyLead" class="pl-btn" wire:confirm="Qualify this lead? Proposal creation will begin.">✅ Qualify & Proceed</button>
                <button @click="showLostForm = !showLostForm" class="pl-btn pl-btn--outline-red" type="button">❌ Mark Lost</button>
            </div>
        </div>
    @endif

    {{-- ═══ PROPOSAL FORM (Qualified) ═══ --}}
    @if($currentStatus === 'qualified')
        <div class="pl-card">
            <div class="pl-card__head">📄 Create Professional Proposal</div>
            <p class="pl-card__sub">সকল তথ্য পূরণ করুন। প্রপোসাল A4 PDF হিসেবে ডাউনলোড করা যাবে।</p>
            <form wire:submit.prevent="saveProposal" class="pl-form" x-data="{ features: @entangle('proposalData.features') }">

                {{-- Client Info --}}
                <fieldset class="pl-fieldset">
                    <legend>🏢 Client Information</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group"><label>Institute / Client Name <abbr>*</abbr></label>
                            <input type="text" wire:model="proposalData.client_name" required></div>
                        <div class="pl-input-group"><label>Contact Person</label>
                            <input type="text" wire:model="proposalData.contact_person"></div>
                    </div>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group"><label>Designation</label>
                            <input type="text" wire:model="proposalData.contact_designation"></div>
                        <div class="pl-input-group"><label>Address</label>
                            <input type="text" wire:model="proposalData.client_address"></div>
                    </div>
                </fieldset>

                {{-- Proposal Info --}}
                <fieldset class="pl-fieldset">
                    <legend>📝 Proposal Details</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group"><label>Proposal Title <abbr>*</abbr></label>
                            <input type="text" wire:model="proposalData.title" required></div>
                        <div class="pl-input-group"><label>Subject Line</label>
                            <input type="text" wire:model="proposalData.subject"></div>
                    </div>
                    <div class="pl-input-group"><label>Introduction Text</label>
                        <textarea wire:model="proposalData.introduction" rows="3"></textarea></div>
                    <div class="pl-input-group"><label>Solution / Scope Description</label>
                        <textarea wire:model="proposalData.solution_description" rows="3"></textarea></div>
                </fieldset>

                {{-- Modules --}}
                <fieldset class="pl-fieldset">
                    <legend>📦 ERP Modules</legend>
                    <div class="pl-module-grid">
                        @foreach($erpModules as $slug => $name)
                            <label class="pl-module-item">
                                <input type="checkbox" wire:model="proposalData.modules" value="{{ $slug }}">
                                <span class="pl-module-item__box"></span>
                                <span class="pl-module-item__text">{{ $name }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>

                {{-- Key Features (Repeater) --}}
                <fieldset class="pl-fieldset">
                    <legend>⭐ Key Features</legend>
                    <template x-for="(feature, index) in features" :key="index">
                        <div style="display:flex;gap:8px;align-items:flex-start;margin-bottom:8px;padding:8px 10px;background:var(--pl-bg);border-radius:8px">
                            <div style="flex:1;display:flex;gap:8px">
                                <div class="pl-input-group" style="flex:0 0 35%;margin:0"><label>Title</label>
                                    <input type="text" x-model="features[index].title" placeholder="Feature title"></div>
                                <div class="pl-input-group" style="flex:1;margin:0"><label>Description</label>
                                    <input type="text" x-model="features[index].description" placeholder="Short description"></div>
                            </div>
                            <button type="button" @click="features.splice(index, 1)" style="margin-top:18px;background:none;border:none;color:var(--pl-red);cursor:pointer;font-size:16px" title="Remove">✕</button>
                        </div>
                    </template>
                    <button type="button" @click="features.push({title:'', description:''})" class="pl-btn-sm" style="margin-top:4px">+ Add Feature</button>
                </fieldset>

                {{-- Technical --}}
                <fieldset class="pl-fieldset">
                    <legend>🖥️ Technical Specification</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group"><label>Frontend</label>
                            <input type="text" wire:model="proposalData.tech_frontend"></div>
                        <div class="pl-input-group"><label>Backend</label>
                            <input type="text" wire:model="proposalData.tech_backend"></div>
                        <div class="pl-input-group"><label>Database</label>
                            <input type="text" wire:model="proposalData.tech_database"></div>
                        <div class="pl-input-group"><label>Access</label>
                            <input type="text" wire:model="proposalData.tech_access"></div>
                    </div>
                </fieldset>

                {{-- Pricing --}}
                <fieldset class="pl-fieldset pl-fieldset--accent">
                    <legend>💰 Pricing</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group"><label>One-Time Setup Cost (৳)</label>
                            <input type="number" wire:model="proposalData.setup_cost" step="0.01" placeholder="e.g. 20000"></div>
                        <div class="pl-input-group"><label>Monthly Fee (৳)</label>
                            <input type="number" wire:model="proposalData.monthly_fee" step="0.01" placeholder="e.g. 5000"></div>
                    </div>
                    <div class="pl-grid pl-grid--3">
                        <div class="pl-input-group"><label>Total / Base Price (৳) <abbr>*</abbr></label>
                            <input type="number" wire:model="proposalData.base_price" required min="1" step="0.01" placeholder="Grand total"></div>
                        <div class="pl-input-group"><label>Discount (%)</label>
                            <input type="number" wire:model="proposalData.discount_percent" min="0" max="100" step="0.5"></div>
                        <div class="pl-input-group"><label>Implementation (Days)</label>
                            <input type="number" wire:model="proposalData.implementation_days" min="1"></div>
                    </div>
                </fieldset>

                {{-- Terms --}}
                <fieldset class="pl-fieldset">
                    <legend>📋 Terms & Conditions</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group"><label>Payment Terms</label>
                            <textarea wire:model="proposalData.payment_terms" rows="3"></textarea></div>
                        <div class="pl-input-group"><label>Support Terms</label>
                            <textarea wire:model="proposalData.support_terms" rows="3"></textarea></div>
                    </div>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group"><label>Validity (Days)</label>
                            <input type="number" wire:model="proposalData.validity_days" min="1"></div>
                        <div class="pl-input-group"><label>Special Notes</label>
                            <input type="text" wire:model="proposalData.special_notes" placeholder="Additional notes"></div>
                    </div>
                </fieldset>

                {{-- Prepared By --}}
                <fieldset class="pl-fieldset">
                    <legend>✍️ Prepared By</legend>
                    <div class="pl-grid pl-grid--2">
                        <div class="pl-input-group"><label>Name</label>
                            <input type="text" wire:model="proposalData.prepared_by"></div>
                        <div class="pl-input-group"><label>Designation</label>
                            <input type="text" wire:model="proposalData.prepared_by_designation" placeholder="e.g. Sales Manager"></div>
                    </div>
                </fieldset>

                <div class="pl-form-footer">
                    <button type="submit" class="pl-btn" wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveProposal">📄 Save & Create Proposal</span>
                        <span wire:loading wire:target="saveProposal">⏳ Creating...</span>
                    </button>
                    <button @click="showLostForm = !showLostForm" class="pl-btn pl-btn--outline-red" type="button">❌ Mark Lost</button>
                </div>
            </form>
        </div>
    @endif

    {{-- ═══ PROPOSAL VIEW + SEND (Proposal stage) ═══ --}}
    @if($currentStatus === 'proposal')
        @php $pd = $lead->proposal_data ?? []; $modules = $pd['modules'] ?? []; $base = (float)($pd['base_price'] ?? 0); $disc = (float)($pd['discount_percent'] ?? 0); $final = max(0, $base - ($base * $disc / 100)); $setup = (float)($pd['setup_cost'] ?? 0); $monthly = (float)($pd['monthly_fee'] ?? 0); @endphp
        <div class="pl-card">
            <div class="pl-card__head">📄 Proposal Summary</div>
            <div class="pl-data-grid">
                <div class="pl-data"><label>Title</label><span>{{ $pd['title'] ?? '—' }}</span></div>
                <div class="pl-data"><label>Client</label><span>{{ $pd['client_name'] ?? $lead->name }}</span></div>
                @if($setup > 0)<div class="pl-data"><label>Setup Cost</label><span>৳{{ number_format($setup) }}</span></div>@endif
                @if($monthly > 0)<div class="pl-data"><label>Monthly Fee</label><span>৳{{ number_format($monthly) }}/mo</span></div>@endif
                <div class="pl-data"><label>Total Price</label><span>৳{{ number_format($base) }}</span></div>
                @if($disc > 0)<div class="pl-data"><label>Discount</label><span>{{ $disc }}%</span></div>@endif
                <div class="pl-data"><label>Final Price</label><span style="color:#059669;font-size:16px;font-weight:700">৳{{ number_format($final) }}</span></div>
                <div class="pl-data"><label>Implementation</label><span>{{ $pd['implementation_days'] ?? '—' }} days</span></div>
                <div class="pl-data"><label>Validity</label><span>{{ $pd['validity_days'] ?? '—' }} days</span></div>
            </div>
            @if(!empty($modules))
                <div class="pl-data-block"><label>Selected Modules</label>
                    <div class="pl-chip-list">@foreach($modules as $m)<span class="pl-chip">{{ $erpModules[$m] ?? $m }}</span>@endforeach</div>
                </div>
            @endif
            @if(!empty($pd['payment_terms']))<div class="pl-data-block"><label>Payment Terms</label><p>{{ $pd['payment_terms'] }}</p></div>@endif
            <div class="pl-form-footer">
                <a href="{{ route('lead.proposal-report', $lead) }}" target="_blank" class="pl-btn-sm pl-btn-sm--accent" style="padding:9px 20px;font-size:13px;border-radius:7px">📄 Download Proposal</a>
                <button wire:click="markProposalSent" class="pl-btn" wire:confirm="Mark proposal as sent and move to Negotiation?">📧 Mark Sent & Start Negotiation</button>
                <button @click="showLostForm = !showLostForm" class="pl-btn pl-btn--outline-red" type="button">❌ Mark Lost</button>
            </div>
        </div>
    @endif

    {{-- ═══ NEGOTIATION (Negotiation stage) ═══ --}}
    @if($currentStatus === 'negotiation')
        {{-- Existing Negotiation Logs --}}
        @php $logs = $lead->negotiationLogs()->with('logger')->get(); @endphp
        @if($logs->count() > 0)
            <div class="pl-card">
                <div class="pl-card__head">📜 Negotiation History ({{ $logs->count() }})</div>
                @foreach($logs as $log)
                    <div style="padding:10px 12px;background:var(--pl-bg);border-radius:8px;margin-bottom:8px;border-left:3px solid {{ $log->client_response === 'positive' ? '#22c55e' : ($log->client_response === 'negative' ? '#ef4444' : '#f59e0b') }}">
                        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:4px">
                            <span style="font-weight:700;font-size:12px">{{ \App\Models\NegotiationLog::TYPE_LABELS[$log->discussion_type] ?? $log->discussion_type }}</span>
                            <span style="font-size:11px;color:var(--pl-text3)">{{ $log->discussion_date->format('d M Y, h:i A') }}</span>
                        </div>
                        <p style="margin:2px 0;font-size:13px;color:var(--pl-text2)">{{ $log->summary }}</p>
                        <div style="display:flex;gap:12px;flex-wrap:wrap;margin-top:6px;font-size:11px;color:var(--pl-text3)">
                            @if($log->counter_offer)<span>💰 Counter: ৳{{ number_format($log->counter_offer) }}</span>@endif
                            @if($log->client_response)<span>📊 {{ \App\Models\NegotiationLog::RESPONSE_LABELS[$log->client_response] ?? $log->client_response }}</span>@endif
                            @if($log->next_action)<span>➡️ {{ $log->next_action }}</span>@endif
                            <span>by {{ $log->logger?->name }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Add Negotiation Log Form --}}
        <div class="pl-card pl-card--dashed">
            <div class="pl-card__head">➕ Add Negotiation Entry</div>
            <form wire:submit.prevent="addNegotiationLog" class="pl-form">
                <div class="pl-grid pl-grid--2">
                    <div class="pl-input-group"><label>Discussion Date & Time</label>
                        <input type="datetime-local" wire:model="negotiationData.discussion_date"></div>
                    <div class="pl-input-group"><label>Discussion Type</label>
                        <select wire:model="negotiationData.discussion_type">
                            @foreach(\App\Models\NegotiationLog::TYPE_LABELS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select></div>
                </div>
                <div class="pl-input-group"><label>Discussion Summary <abbr>*</abbr></label>
                    <textarea wire:model="negotiationData.summary" rows="3" required placeholder="আলোচনার বিষয়বস্তু..."></textarea></div>
                <div class="pl-grid pl-grid--2">
                    <div class="pl-input-group"><label>Counter Offer (৳)</label>
                        <input type="number" wire:model="negotiationData.counter_offer" step="0.01" placeholder="নতুন মূল্য প্রস্তাব"></div>
                    <div class="pl-input-group"><label>Client Response</label>
                        <select wire:model="negotiationData.client_response">
                            <option value="">— Select —</option>
                            @foreach(\App\Models\NegotiationLog::RESPONSE_LABELS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select></div>
                </div>
                <div class="pl-grid pl-grid--2">
                    <div class="pl-input-group"><label>Module Changes</label>
                        <input type="text" wire:model="negotiationData.module_changes" placeholder="কোন মডিউল বাদ/যোগ হয়েছে"></div>
                    <div class="pl-input-group"><label>Next Action</label>
                        <input type="text" wire:model="negotiationData.next_action" placeholder="পরবর্তী পদক্ষেপ"></div>
                </div>
                <div class="pl-form-footer">
                    <button type="submit" class="pl-btn">🤝 Add Log Entry</button>
                    <button wire:click="markAsWon" class="pl-btn" style="background:linear-gradient(135deg,#22c55e,#16a34a)" wire:confirm="Confirm this lead as Won? Trial Client ও Migration তৈরি হবে।">🏆 Mark as Won</button>
                    <button @click="showLostForm = !showLostForm" class="pl-btn pl-btn--outline-red" type="button">❌ Mark Lost</button>
                </div>
            </form>
        </div>
    @endif

    {{-- ═══ WON — Client & Migration Transfer ═══ --}}
    @if($currentStatus === 'won')
        @php
            $wo = $lead->workOrders()->latest()->first();
            $client = \App\Models\Client::where('lead_id', $lead->id)->first();
            $migration = $client ? \App\Models\CrmMigration::where('client_id', $client->id)->latest()->first() : null;
        @endphp

        {{-- Success Banner --}}
        <div class="pl-card" style="border:2px solid #22c55e;background:linear-gradient(135deg,#f0fdf4,#dcfce7)">
            <div class="pl-card__head" style="color:#16a34a">🏆 Lead Won — Congratulations!</div>
            <p class="pl-card__sub" style="color:#15803d">এই লীডটি সফলভাবে Won হয়েছে। Trial Client হিসেবে Migration শুরু হয়েছে।</p>
        </div>

        {{-- Client Transfer Card --}}
        @if($client)
            <div class="pl-card">
                <div class="pl-card__head">👤 Trial Client Created</div>
                <div class="pl-data-grid">
                    <div class="pl-data"><label>Client ID</label><span style="font-weight:800;color:#6366f1;font-size:15px">{{ $client->client_id }}</span></div>
                    <div class="pl-data"><label>Client Name</label><span style="font-weight:700">{{ $client->name }}</span></div>
                    <div class="pl-data"><label>Pipeline Stage</label><span>{{ \App\Models\Client::PIPELINE_STAGE_LABELS[$client->pipeline_stage] ?? $client->pipeline_stage }}</span></div>
                    <div class="pl-data"><label>Package Price</label><span style="color:#059669;font-size:14px;font-weight:700">৳{{ number_format($client->package_price ?? 0) }}</span></div>
                    <div class="pl-data"><label>Activation</label><span>{{ \App\Models\Client::ACTIVATION_STATUS_LABELS[$client->activation_status] ?? $client->activation_status }}</span></div>
                    <div class="pl-data"><label>Contract Start</label><span>{{ $client->contract_start?->format('d M Y') ?? '—' }}</span></div>
                </div>
            </div>
        @endif

        {{-- Migration Progress Card --}}
        @if($migration)
            <div class="pl-card">
                <div class="pl-card__head">🔄 Migration Progress</div>
                @php
                    $stepProgress = $migration->getStepProgress();
                    $currentIdx = $migration->getCurrentStepIndex();
                @endphp
                <div style="display:flex;flex-wrap:wrap;gap:6px;margin:10px 0">
                    @foreach($stepProgress as $i => $step)
                        <div style="display:flex;align-items:center;gap:5px;padding:5px 10px;border-radius:16px;font-size:11px;font-weight:600;
                            {{ $step['done'] ? 'background:#dcfce7;color:#16a34a;border:1px solid #bbf7d0' : ($step['current'] ? 'background:#dbeafe;color:#2563eb;border:1px solid #93c5fd' : 'background:#f3f4f6;color:#9ca3af;border:1px solid #e5e7eb') }}">
                            @if($step['done'])✅ @elseif($step['current'])🔵 @else⬜ @endif
                            {{ $step['label'] }}
                        </div>
                    @endforeach
                </div>
                <div class="pl-data-grid" style="margin-top:8px">
                    <div class="pl-data"><label>Current Step</label><span style="font-weight:700">{{ \App\Models\CrmMigration::STEP_LABELS[$migration->current_step] ?? $migration->current_step }}</span></div>
                    <div class="pl-data"><label>Progress</label><span style="font-weight:700;color:#6366f1">{{ $migration->progress_percent }}%</span></div>
                    <div class="pl-data"><label>Start Date</label><span>{{ $migration->migration_start_date?->format('d M Y') ?? '—' }}</span></div>
                    <div class="pl-data"><label>Expected End</label><span>{{ $migration->migration_end_date?->format('d M Y') ?? '—' }}</span></div>
                </div>
                <div class="pl-form-footer" style="margin-top:10px">
                    <a href="/admin/crm-migrations/{{ $migration->id }}" class="pl-btn" style="background:linear-gradient(135deg,#6366f1,#4f46e5);text-decoration:none">
                        🔄 View Migration Details
                    </a>
                </div>
            </div>
        @endif

        {{-- Work Order Card --}}
        @if($wo)
            <div class="pl-card">
                <div class="pl-card__head">📋 Work Order</div>
                <div class="pl-data-grid">
                    <div class="pl-data"><label>Work Order #</label><span style="font-weight:700;color:#059669">{{ $wo->order_number }}</span></div>
                    <div class="pl-data"><label>Client</label><span>{{ $wo->client_name }}</span></div>
                    <div class="pl-data"><label>Total Amount</label><span style="font-size:15px;color:#059669;font-weight:700">৳{{ number_format($wo->total_amount) }}</span></div>
                    <div class="pl-data"><label>Status</label><span>{{ \App\Models\WorkOrder::STATUS_LABELS[$wo->status] ?? $wo->status }}</span></div>
                    <div class="pl-data"><label>Start Date</label><span>{{ $wo->start_date?->format('d M Y') ?? '—' }}</span></div>
                    <div class="pl-data"><label>Expected Delivery</label><span>{{ $wo->expected_delivery?->format('d M Y') ?? '—' }}</span></div>
                </div>
                @if($wo->deliverables)<div class="pl-data-block"><label>Deliverables</label><p>{{ $wo->deliverables }}</p></div>@endif
                @if($wo->payment_terms)<div class="pl-data-block"><label>Payment Terms</label><p>{{ $wo->payment_terms }}</p></div>@endif
            </div>
        @endif
    @endif

    {{-- LOST --}}
    @if(!$isLost && $currentStatus !== 'new')
        <div x-show="showLostForm" x-collapse class="pl-card pl-card--red">
            <div class="pl-card__head">❌ Mark as Lost</div>
            <div class="pl-input-group">
                <label>Reason <abbr>*</abbr></label>
                <textarea wire:model="lostReason" rows="2" placeholder="Why was this lead lost?"></textarea>
            </div>
            <button wire:click="markLost" class="pl-btn pl-btn--red" wire:confirm="Confirm lost?">Confirm Lost</button>
        </div>
    @endif
    @endif {{-- end !viewingStep --}}
</div>
</div>

<style>
/* ═══════════════════════════════════════════
   PIPELINE VIEW — MOBILE-FIRST
   ═══════════════════════════════════════════ */
:root{--pl-primary:#6366f1;--pl-primary-light:#818cf8;--pl-green:#22c55e;--pl-red:#ef4444;--pl-border:#e5e7eb;--pl-bg:#f9fafb;--pl-card:#fff;--pl-text:#111827;--pl-text2:#6b7280;--pl-text3:#9ca3af;--pl-radius:10px;--pl-radius-sm:7px;--pl-shadow:0 1px 3px rgba(0,0,0,.05)}

/* Container — outer shell (Filament may reset its styles) */
.pl{width:100%;font-family:inherit;font-size:13px;line-height:1.5;color:var(--pl-text)}
/* Inner wrapper — carries actual padding (safe from Filament !important resets) */
.pl-inner{padding:12px 16px;box-sizing:border-box;overflow-x:hidden}
.pl-inner *,.pl-inner *::before,.pl-inner *::after{box-sizing:border-box}
/* Force padding on Filament's own page content container on mobile */
@media(max-width:767px){
    .fi-page-content{padding-left:12px !important;padding-right:12px !important}
    .fi-main{padding-left:4px !important;padding-right:4px !important}
}

/* ─── HEADER ─── */
.pl-head{display:flex;flex-direction:column;gap:10px;padding:12px 14px;background:linear-gradient(135deg,#f0f3ff 0%,#e8ecff 100%);border-radius:var(--pl-radius);margin-bottom:10px}
.pl-head-info{display:flex;align-items:center;gap:10px}
.pl-head-actions{display:flex;align-items:center;gap:6px;flex-wrap:wrap}
.pl-avatar{width:40px;height:40px;border-radius:10px;background:linear-gradient(135deg,var(--pl-primary),#8b5cf6);color:#fff;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:15px;flex-shrink:0;letter-spacing:-.5px}
.pl-avatar{width:56px;height:56px;background:linear-gradient(135deg,var(--pl-primary),#8b5cf6);color:#fff;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;box-shadow:0 4px 10px rgba(99,102,241,.2);flex-shrink:0}
.pl-meta{min-width:0}
.pl-name{margin:0;font-size:16px;font-weight:700;color:var(--pl-text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pl-detail{margin:1px 0 0;font-size:11px;color:var(--pl-text2);white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.pl-pill{padding:2px 10px;border-radius:20px;font-size:10px;font-weight:700;white-space:nowrap;letter-spacing:.2px}
.pl-pill--hot{background:#fef2f2;color:#dc2626}.pl-pill--warm{background:#fffbeb;color:#d97706}.pl-pill--cold{background:#f1f5f9;color:#64748b}.pl-pill--val{background:#ecfdf5;color:#059669}
.pl-btn-sm{padding:4px 10px;border-radius:6px;font-size:10px;font-weight:600;text-decoration:none;border:1px solid var(--pl-border);background:var(--pl-card);color:var(--pl-text);transition:.15s}
.pl-btn-sm:hover{background:var(--pl-bg)}.pl-btn-sm--ghost{background:transparent;color:var(--pl-text3);border-color:transparent}
.pl-btn-sm--accent{background:linear-gradient(135deg,var(--pl-primary),#7c3aed);color:#fff;border-color:transparent;box-shadow:0 2px 6px rgba(99,102,241,.2)}.pl-btn-sm--accent:hover{opacity:.9;color:#fff;transform:translateY(-1px)}

/* ─── BANNER ─── */
.pl-banner{padding:8px 12px;border-radius:var(--pl-radius-sm);margin-bottom:10px;font-size:12px}
.pl-banner--lost{background:#fef2f2;border:1px solid #fecaca;color:#991b1b}

/* ─── STEPPER ─── */
.pl-steps{display:flex;align-items:flex-start;padding:12px;background:var(--pl-card);border-radius:var(--pl-radius);box-shadow:var(--pl-shadow);margin-bottom:12px;overflow-x:auto;-webkit-overflow-scrolling:touch;scrollbar-width:none}
.pl-steps::-webkit-scrollbar{display:none}
.pl-step{display:flex;flex-direction:column;align-items:center;gap:3px;min-width:52px;flex-shrink:0}
.pl-step__dot{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700;z-index:2;transition:.3s ease}
.pl-step--done .pl-step__dot{background:linear-gradient(135deg,var(--pl-green),#16a34a);box-shadow:0 2px 8px rgba(34,197,94,.2)}
.pl-step--cur .pl-step__dot{background:linear-gradient(135deg,var(--pl-primary),#8b5cf6);box-shadow:0 2px 10px rgba(99,102,241,.3);animation:stepPulse 2s ease-in-out infinite}
.pl-step:not(.pl-step--done):not(.pl-step--cur) .pl-step__dot{background:#e5e7eb;color:#9ca3af}
.pl-step--clickable{cursor:pointer}
.pl-step--clickable:hover .pl-step__dot{transform:scale(1.15);box-shadow:0 3px 12px rgba(34,197,94,.35)}
.pl-step--clickable:hover .pl-step__label{color:var(--pl-green);text-decoration:underline}
.pl-step__num{font-size:11px}.pl-step__label{font-size:8px;font-weight:600;text-align:center;color:var(--pl-text3);white-space:nowrap;text-transform:uppercase;letter-spacing:.3px}
.pl-step--done .pl-step__label{color:var(--pl-green)}.pl-step--cur .pl-step__label{color:var(--pl-primary);font-weight:700}
.pl-step__line{flex:1;height:2px;background:#e5e7eb;margin-top:14px;min-width:4px;border-radius:1px}
.pl-step__line--done{background:linear-gradient(90deg,var(--pl-green),#86efac)}
.pl-step__line--cur{background:linear-gradient(90deg,var(--pl-primary) 40%,#e5e7eb)}

/* ─── STATUS BAR ─── */
.pl-status{display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:4px;padding:8px 12px;background:var(--pl-bg);border:1px solid var(--pl-border);border-radius:var(--pl-radius-sm);margin-bottom:12px;font-size:12px}
.pl-status__dot{width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:6px}
.pl-status__dot--new{background:#9ca3af}.pl-status__dot--contacted{background:#3b82f6}.pl-status__dot--qualified{background:var(--pl-primary)}.pl-status__dot--proposal{background:#f59e0b}.pl-status__dot--negotiation{background:#8b5cf6}.pl-status__dot--won{background:var(--pl-green)}.pl-status__dot--lost{background:var(--pl-red)}
.pl-status__next{color:var(--pl-primary);font-weight:500}.pl-status__won{color:var(--pl-green);font-weight:600}.pl-status__time{color:var(--pl-text3);font-size:11px}

/* ─── CARD ─── */
.pl-card{background:var(--pl-card);border-radius:var(--pl-radius);padding:16px;box-shadow:var(--pl-shadow);margin-bottom:12px}
.pl-card__head{font-size:15px;font-weight:700;color:var(--pl-text);margin-bottom:4px}
.pl-card__sub{font-size:12px;color:var(--pl-text2);margin:0 0 32px}
.pl-card--dashed{border:2px dashed var(--pl-primary);background:#faf5ff}
.pl-card--red{border:1px solid #fecaca;background:#fff5f5}

/* ─── DATA DISPLAY ─── */
.pl-data-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-bottom:12px}
.pl-data{padding:8px 10px;background:var(--pl-bg);border-radius:6px}
.pl-data label{display:block;font-size:10px;color:var(--pl-text3);text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:2px}
.pl-data span{font-size:13px;font-weight:600;color:var(--pl-text)}
.pl-data-block{padding:8px 12px;background:var(--pl-bg);border-radius:6px;margin-bottom:8px}
.pl-data-block label{display:block;font-size:10px;color:var(--pl-text3);text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:2px}
.pl-data-block p{margin:4px 0 0;font-size:13px;color:var(--pl-text2);line-height:1.5}
.pl-data-block--accent{border-left:4px solid var(--pl-primary)}
.pl-chip-list{display:flex;flex-wrap:wrap;gap:6px;margin-top:4px}
.pl-chip{padding:4px 10px;background:#ede9fe;color:#6d28d9;border-radius:6px;font-size:11px;font-weight:600}
.pl-progress{height:20px;background:#e5e7eb;border-radius:10px;overflow:hidden;margin-top:6px}
.pl-progress__bar{height:100%;background:linear-gradient(90deg,var(--pl-primary),#8b5cf6);border-radius:10px;display:flex;align-items:center;justify-content:center;font-size:10px;font-weight:700;color:#fff;min-width:32px;transition:width .5s ease}
.pl-progress__note{font-style:italic;color:var(--pl-text2);margin-top:4px}

/* ─── FORM ─── */
.pl-form{display:flex;flex-direction:column;gap:0;padding:0}
.pl-fieldset{border:none;padding:0;margin:0 0 4px;padding-bottom:16px;border-bottom:1px solid var(--pl-border)}
.pl-fieldset:last-of-type{border-bottom:none;margin-bottom:0;padding-bottom:0}
.pl-fieldset legend{font-size:14px;font-weight:700;color:var(--pl-text);margin-bottom:12px;padding:0;display:flex;align-items:center;gap:6px}
.pl-fieldset--accent{background:linear-gradient(135deg,#faf5ff,#f3e8ff);padding:16px;border-radius:var(--pl-radius);border:1px solid #e9d5ff;margin-top:8px}

/* Grids — Mobile first = single column */
.pl-grid{display:grid;gap:10px;margin-bottom:10px}
.pl-grid--2{grid-template-columns:1fr}
.pl-grid--3{grid-template-columns:1fr}

/* Input groups */
.pl-input-group{display:flex;flex-direction:column;gap:3px;margin-bottom:6px}
.pl-input-group:last-child{margin-bottom:0}
.pl-input-group label{font-size:11px;font-weight:600;color:var(--pl-text2);display:flex;align-items:center;gap:2px}
.pl-input-group label abbr{color:var(--pl-red);text-decoration:none;font-weight:700}
.pl-input-group input[type="text"],
.pl-input-group input[type="number"],
.pl-input-group input[type="date"],
.pl-input-group input[type="datetime-local"],
.pl-input-group select,
.pl-input-group textarea{
    width:100%;padding:8px 10px;border:1.5px solid var(--pl-border);border-radius:var(--pl-radius-sm);font-size:13px;font-family:inherit;background:var(--pl-card);color:var(--pl-text);outline:none;transition:border .15s,box-shadow .15s;-webkit-appearance:none;appearance:none}
.pl-input-group input:focus,.pl-input-group select:focus,.pl-input-group textarea:focus{border-color:var(--pl-primary);box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.pl-input-group input::placeholder,.pl-input-group textarea::placeholder{color:#c7c7cc}
.pl-input-group select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%236b7280' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 10px center;padding-right:28px}

/* Toggle switch */
.pl-toggle{display:flex;align-items:center;gap:8px;cursor:pointer;user-select:none;font-size:12px;color:var(--pl-text2)}
.pl-toggle input{display:none}
.pl-toggle__track{width:36px;height:20px;background:#d1d5db;border-radius:10px;position:relative;transition:.2s}
.pl-toggle input:checked+.pl-toggle__track{background:var(--pl-primary)}
.pl-toggle__thumb{width:16px;height:16px;background:#fff;border-radius:50%;position:absolute;top:2px;left:2px;transition:.2s;box-shadow:0 1px 2px rgba(0,0,0,.15)}
.pl-toggle input:checked+.pl-toggle__track .pl-toggle__thumb{left:18px}

/* Range input with unit */
.pl-range-wrap{display:flex;align-items:stretch;border:1.5px solid var(--pl-border);border-radius:var(--pl-radius-sm);overflow:hidden;transition:border .15s,box-shadow .15s}
.pl-range-wrap:focus-within{border-color:var(--pl-primary);box-shadow:0 0 0 3px rgba(99,102,241,.1)}
.pl-range-input{border:none!important;box-shadow:none!important;border-radius:0!important;flex:1;padding:8px 10px;font-size:13px;outline:none;background:var(--pl-card);color:var(--pl-text)}
.pl-range-unit{display:flex;align-items:center;padding:0 10px;background:var(--pl-bg);font-size:12px;font-weight:700;color:var(--pl-text2);border-left:1px solid var(--pl-border)}

/* Module grid */
.pl-module-grid{display:grid;grid-template-columns:1fr;gap:4px;margin-top:4px}
.pl-module-item{display:flex;align-items:center;gap:6px;padding:7px 10px;border:1.5px solid var(--pl-border);border-radius:var(--pl-radius-sm);cursor:pointer;font-size:12px;transition:.15s;user-select:none}
.pl-module-item:hover{border-color:var(--pl-primary-light);background:#f5f3ff}
.pl-module-item input{display:none}
.pl-module-item__box{width:16px;height:16px;border:2px solid #d1d5db;border-radius:4px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:.15s;position:relative}
.pl-module-item input:checked~.pl-module-item__box{background:var(--pl-primary);border-color:var(--pl-primary)}
.pl-module-item input:checked~.pl-module-item__box::after{content:'';width:5px;height:9px;border:solid #fff;border-width:0 2px 2px 0;transform:rotate(45deg);position:absolute;top:1px}
.pl-module-item__text{color:var(--pl-text2);font-weight:500}
.pl-module-item input:checked~.pl-module-item__text{color:var(--pl-primary);font-weight:600}

/* ─── BUTTONS ─── */
.pl-form-footer{display:flex;align-items:center;gap:8px;flex-wrap:wrap;padding-top:12px}
.pl-btn{padding:9px 20px;background:linear-gradient(135deg,var(--pl-primary),#8b5cf6);color:#fff;border:none;border-radius:var(--pl-radius-sm);font-size:13px;font-weight:600;cursor:pointer;transition:.2s;box-shadow:0 2px 8px rgba(99,102,241,.2);font-family:inherit}
.pl-btn:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(99,102,241,.3)}.pl-btn:disabled{opacity:.5;cursor:not-allowed;transform:none}
.pl-btn--outline-red{background:var(--pl-card);color:var(--pl-red);border:1.5px solid #fecaca;box-shadow:none}
.pl-btn--outline-red:hover{background:#fef2f2;transform:translateY(-1px)}
.pl-btn--red{background:var(--pl-red);color:#fff;box-shadow:0 2px 6px rgba(239,68,68,.2)}

/* ─── ANIMATIONS ─── */
@keyframes stepPulse{0%,100%{box-shadow:0 2px 10px rgba(99,102,241,.3),0 0 0 0 rgba(99,102,241,.2)}50%{box-shadow:0 2px 10px rgba(99,102,241,.3),0 0 0 6px rgba(99,102,241,0)}}

/* ═══════════════════════════════════════════
   DESKTOP (≥768px)
   ═══════════════════════════════════════════ */
@media(min-width:768px){
    .pl-inner{padding:24px 32px; max-width:100%}
    .pl-head{flex-direction:row;align-items:center;justify-content:space-between;padding:16px 20px}
    .pl-grid--2{grid-template-columns:1fr 1fr}

    .pl-grid--3{grid-template-columns:1fr 1fr 1fr}
    .pl-input-group--narrow{max-width:180px}
    .pl-data-grid{grid-template-columns:repeat(4,1fr)}
    .pl-module-grid{grid-template-columns:repeat(3,1fr)}
    .pl-step{min-width:64px}
    .pl-step__dot{width:34px;height:34px;font-size:15px}
    .pl-step__label{font-size:9px}
    .pl-card{padding:16px 20px}
}

/* ═══════════════════════════════════════════
   LARGE DESKTOP (≥1024px)
   ═══════════════════════════════════════════ */
@media(min-width:1024px){
    .pl-module-grid{grid-template-columns:repeat(4,1fr)}
}

/* ═══════════════════════════════════════════
   DARK MODE
   ═══════════════════════════════════════════ */
.dark{--pl-border:#374151;--pl-bg:#111827;--pl-card:#1e293b;--pl-text:#f3f4f6;--pl-text2:#9ca3af;--pl-text3:#6b7280;--pl-shadow:none}
.dark .pl-head{background:linear-gradient(135deg,#1e293b,#1a2332)}
.dark .pl-steps{border:1px solid var(--pl-border)}
.dark .pl-step:not(.pl-step--done):not(.pl-step--cur) .pl-step__dot{background:#374151;color:#6b7280}
.dark .pl-step__line{background:#374151}
.dark .pl-step__line--cur{background:linear-gradient(90deg,var(--pl-primary) 40%,#374151)}
.dark .pl-pill--hot{background:#451a1a}.dark .pl-pill--warm{background:#451a00}.dark .pl-pill--cold{background:#1e293b}.dark .pl-pill--val{background:#022c22}
.dark .pl-banner--lost{background:#451a1a;border-color:#7f1d1d;color:#fca5a5}
.dark .pl-card--dashed{background:#1a1033;border-color:#4f46e5}
.dark .pl-card--red{background:#1a1111;border-color:#7f1d1d}
.dark .pl-chip{background:#312e81;color:#a5b4fc}
.dark .pl-progress{background:#374151}
.dark .pl-module-item{border-color:#374151;color:#e5e7eb}
.dark .pl-module-item:hover{background:#312e81}
.dark .pl-module-item__box{border-color:#4b5563}
.dark .pl-toggle__track{background:#4b5563}
.dark .pl-range-unit{background:#111827;border-color:#374151}
.dark .pl-fieldset--accent{background:linear-gradient(135deg,#1a1033,#1e1145);border-color:#312e81}
.dark .pl-btn-sm{background:#374151;color:#e5e7eb;border-color:#4b5563}
.dark .pl-btn--outline-red{background:#1e293b;border-color:#7f1d1d;color:#fca5a5}
.dark .pl-input-group select{background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%239ca3af' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E")}
</style>
</x-filament-panels::page>
