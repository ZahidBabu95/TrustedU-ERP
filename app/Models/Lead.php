<?php

namespace App\Models;

use App\Models\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use HasFactory, SoftDeletes, HasTeamScope;

    protected $guarded = [];

    protected $casts = [
        'value'              => 'decimal:2',
        'expected_close_date' => 'date',
        'follow_up_date'     => 'date',
        'lost_at'            => 'datetime',
        'contact_report'     => 'array',
        'source_details'     => 'array',
        'proposal_data'      => 'array',
        'status_changed_at'  => 'datetime',
    ];

    // ERP Modules offered (for contact report)
    public const ERP_MODULES = [
        'sis'         => 'Student Information System (SIS)',
        'accounts'    => 'Financial / Accounts Management',
        'hr'          => 'HR & Payroll Management',
        'attendance'  => 'Attendance Management',
        'exam'        => 'Exam & Result Management',
        'library'     => 'Library Management',
        'transport'   => 'Transport Management',
        'hostel'      => 'Hostel / Dormitory Management',
        'sms'         => 'SMS & Communication',
        'website'     => 'Website CMS',
        'admission'   => 'Online Admission',
        'fees'        => 'Fees Collection & Management',
        'report'      => 'Reports & Analytics',
        'inventory'   => 'Inventory Management',
        'certificate' => 'Certificate Management',
        'id_card'     => 'ID Card Generation',
    ];

    // ── Pipeline Stages ──
    public const PIPELINE_NEW_LEAD  = 'new_lead';
    public const PIPELINE_CONTACTED = 'contacted';
    public const PIPELINE_QUALIFIED = 'qualified';
    public const PIPELINE_LOST      = 'lost';

    public const PIPELINE_STAGES = [
        self::PIPELINE_NEW_LEAD,
        self::PIPELINE_CONTACTED,
        self::PIPELINE_QUALIFIED,
    ];

    public const PIPELINE_STAGE_LABELS = [
        'new_lead'  => 'New Lead',
        'contacted' => 'Contact & Engagement',
        'qualified' => 'Qualified',
        'lost'      => 'Lost',
    ];

    public const PIPELINE_STAGE_COLORS = [
        'new_lead'  => '#94a3b8',
        'contacted' => '#3b82f6',
        'qualified' => '#10b981',
        'lost'      => '#ef4444',
    ];

    // ── Status constants (legacy, kept for backward compat) ──
    public const STATUS_NEW = 'new';
    public const STATUS_CONTACTED = 'contacted';
    public const STATUS_QUALIFIED = 'qualified';
    public const STATUS_PROPOSAL = 'proposal';
    public const STATUS_NEGOTIATION = 'negotiation';
    public const STATUS_WON = 'won';
    public const STATUS_LOST = 'lost';

    public const KANBAN_STATUSES = [
        self::STATUS_NEW,
        self::STATUS_CONTACTED,
        self::STATUS_QUALIFIED,
        self::STATUS_PROPOSAL,
        self::STATUS_NEGOTIATION,
    ];

    public const STATUS_LABELS = [
        'new'          => 'New',
        'contacted'    => 'Contacted',
        'qualified'    => 'Qualified',
        'proposal'     => 'Proposal',
        'negotiation'  => 'Negotiation',
        'won'          => 'Won',
        'lost'         => 'Lost',
    ];

    public const STATUS_COLORS = [
        'new'          => '#94a3b8',
        'contacted'    => '#60a5fa',
        'qualified'    => '#10b981',
        'proposal'     => '#f97316',
        'negotiation'  => '#6366f1',
        'won'          => '#22c55e',
        'lost'         => '#ef4444',
    ];

    public const SOURCE_LABELS = [
        'web'       => 'Website',
        'referral'  => 'Referral',
        'social'    => 'Social Media',
        'cold_call' => 'Cold Call',
        'email'     => 'Email',
        'chatbot'   => 'Chatbot',
        'facebook'  => 'Facebook',
        'other'     => 'Other',
    ];

    public const PRIORITY_LABELS = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High Priority',
        'urgent' => 'Urgent',
    ];

    public const INTEREST_LABELS = [
        'cold' => '❄️ Cold',
        'warm' => '🟡 Warm',
        'hot'  => '🔥 Hot',
    ];

    public const INTEREST_COLORS = [
        'cold' => '#94a3b8',
        'warm' => '#f59e0b',
        'hot'  => '#ef4444',
    ];

    public const INSTITUTE_TYPE_LABELS = [
        'school'         => 'School',
        'college'        => 'College',
        'school_college' => 'School & College',
        'madrasah'       => 'Madrasah',
        'university'     => 'University',
        'coaching_center' => 'Coaching Center',
        'other'          => 'Other',
    ];

    // ── Relationships ──
    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function deals()
    {
        return $this->hasMany(Deal::class);
    }

    public function demoRequest()
    {
        return $this->belongsTo(DemoRequest::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function proposals()
    {
        return $this->hasMany(CrmProposal::class);
    }

    public function negotiationLogs()
    {
        return $this->hasMany(NegotiationLog::class)->orderByDesc('discussion_date');
    }

    public function workOrders()
    {
        return $this->hasMany(WorkOrder::class);
    }

    public function activities()
    {
        return $this->hasMany(CrmActivity::class, 'entity_id')
            ->where('entity_type', 'lead')
            ->orderByDesc('created_at');
    }

    public function followUps()
    {
        return $this->hasMany(CrmFollowUp::class, 'entity_id')
            ->where('entity_type', 'lead')
            ->orderBy('scheduled_at');
    }

    // ── Conversion ──

    /**
     * Convert this lead into a CRM Deal.
     */
    public function convertToDeal(?int $assignedTo = null): Deal
    {
        $deal = Deal::create([
            'title'              => ($this->company ?: $this->name) . ' — Deal',
            'company'            => $this->company ?: $this->institute_name,
            'contact_name'       => $this->contact_person ?: $this->name,
            'contact_email'      => $this->email,
            'contact_phone'      => $this->phone,
            'value'              => $this->value,
            'stage'              => 'discovery',
            'pipeline_stage'     => 'proposal_draft',
            'priority'           => $this->priority ?? 'medium',
            'deal_source'        => 'lead',
            'probability'        => 10,
            'lead_id'            => $this->id,
            'assigned_to'        => $assignedTo ?? $this->assigned_to,
            'team_id'            => $this->team_id,
            'expected_close_date' => $this->expected_close_date,
            'modules_required'   => $this->demoRequest?->interested_modules,
            'notes'              => "Converted from Lead #{$this->id}: {$this->name}\n" . ($this->notes ?? ''),
        ]);

        $this->update([
            'status'         => 'won',
            'pipeline_stage' => 'qualified',
        ]);

        // Log activity
        CrmActivity::log('lead', $this->id, 'conversion', "Lead converted to Deal #{$deal->id}", $deal->title);
        CrmActivity::log('deal', $deal->id, 'system', "Deal created from Lead #{$this->id}", $this->name);

        return $deal;
    }

    // ── Helpers ──

    public function isConverted(): bool
    {
        return $this->status === 'won' && $this->deals()->exists();
    }

    public function calculateQualificationScore(): int
    {
        $score = 0;

        // Interest level
        if ($this->interest_level === 'hot') $score += 30;
        elseif ($this->interest_level === 'warm') $score += 15;

        // Institute details filled
        if ($this->institute_name) $score += 10;
        if ($this->institute_type) $score += 5;
        if ($this->student_count) $score += 10;
        if ($this->email) $score += 5;
        if ($this->phone) $score += 5;

        // Has value
        if ($this->value > 0) $score += 10;

        // Has follow-up
        if ($this->follow_up_date) $score += 5;

        // Source quality
        if (in_array($this->source, ['web', 'referral'])) $score += 5;

        // Priority
        if ($this->priority === 'urgent') $score += 10;
        elseif ($this->priority === 'high') $score += 5;

        $this->update(['qualification_score' => min(100, $score)]);

        return min(100, $score);
    }

    // ── Scopes ──

    public function scopeByPipelineStage($query, string $stage)
    {
        return $query->where('pipeline_stage', $stage);
    }

    public function scopeQualified($query)
    {
        return $query->where('pipeline_stage', 'qualified');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('pipeline_stage', ['lost']);
    }
}
