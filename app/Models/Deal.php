<?php

namespace App\Models;

use App\Models\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Deal extends Model
{
    use HasFactory, SoftDeletes, HasTeamScope;

    protected $guarded = [];

    protected $casts = [
        'value'                  => 'decimal:2',
        'expected_close_date'    => 'date',
        'closed_at'              => 'date',
        'approved_at'            => 'datetime',
        'modules_required'       => 'array',
        'previous_software_used' => 'boolean',
        'deed_effective_date'    => 'date',
        'deed_end_date'          => 'date',
        'deed_plan_features'     => 'array',
        'deed_bank_accounts'     => 'array',
        'deed_company_info'      => 'array',
        'deed_monthly_fee'       => 'decimal:2',
        'deed_per_user_rate'     => 'decimal:2',
        'deed_installation_cost' => 'decimal:2',
        'deed_generated_at'      => 'datetime',
    ];

    // ── Deed Status ──
    public const DEED_STATUS_LABELS = [
        'not_created' => '📝 Not Created',
        'draft'       => '📄 Draft',
        'generated'   => '✅ Generated',
        'signed'      => '🖊️ Signed',
        'active'      => '🟢 Active',
        'expired'     => '🔴 Expired',
    ];

    // ── Pipeline Stages ──
    public const PIPELINE_PROPOSAL_DRAFT  = 'proposal_draft';
    public const PIPELINE_PROPOSAL_SENT   = 'proposal_sent';
    public const PIPELINE_NEGOTIATION     = 'negotiation';
    public const PIPELINE_DEAL_WON        = 'deal_won';
    public const PIPELINE_LOST            = 'lost';

    public const PIPELINE_STAGES = [
        self::PIPELINE_PROPOSAL_DRAFT,
        self::PIPELINE_PROPOSAL_SENT,
        self::PIPELINE_NEGOTIATION,
    ];

    public const PIPELINE_STAGE_LABELS = [
        'proposal_draft' => 'Proposal Draft',
        'proposal_sent'  => 'Proposal Sent',
        'negotiation'    => 'Negotiation',
        'deal_won'       => 'Deal Won',
        'lost'           => 'Lost',
    ];

    public const PIPELINE_STAGE_COLORS = [
        'proposal_draft' => '#f97316',
        'proposal_sent'  => '#8b5cf6',
        'negotiation'    => '#6366f1',
        'deal_won'       => '#22c55e',
        'lost'           => '#ef4444',
    ];

    // ── Stage constants (legacy) ──
    public const STAGE_DISCOVERY    = 'discovery';
    public const STAGE_PROSPECTING  = 'prospecting';
    public const STAGE_PROPOSAL     = 'proposal';
    public const STAGE_NEGOTIATION  = 'negotiation';
    public const STAGE_CONTRACT     = 'contract';
    public const STAGE_CLOSED_WON   = 'closed_won';
    public const STAGE_CLOSED_LOST  = 'closed_lost';

    public const KANBAN_STAGES = [
        self::STAGE_DISCOVERY,
        self::STAGE_PROSPECTING,
        self::STAGE_PROPOSAL,
        self::STAGE_NEGOTIATION,
        self::STAGE_CONTRACT,
    ];

    public const STAGE_LABELS = [
        'discovery'    => 'Discovery',
        'prospecting'  => 'Prospecting',
        'proposal'     => 'Proposal',
        'negotiation'  => 'Negotiation',
        'contract'     => 'Contract',
        'closed_won'   => 'Closed Won',
        'closed_lost'  => 'Closed Lost',
    ];

    public const STAGE_COLORS = [
        'discovery'    => '#94a3b8',
        'prospecting'  => '#60a5fa',
        'proposal'     => '#f97316',
        'negotiation'  => '#8b5cf6',
        'contract'     => '#6366f1',
        'closed_won'   => '#10b981',
        'closed_lost'  => '#ef4444',
    ];

    public const STAGE_PROBABILITIES = [
        'discovery'    => 10,
        'prospecting'  => 25,
        'proposal'     => 50,
        'negotiation'  => 70,
        'contract'     => 90,
        'closed_won'   => 100,
        'closed_lost'  => 0,
    ];

    public const SOURCE_LABELS = [
        'lead'     => 'From Lead',
        'direct'   => 'Direct',
        'referral' => 'Referral',
        'upsell'   => 'Upsell',
        'other'    => 'Other',
    ];

    public const PRIORITY_LABELS = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High Priority',
        'urgent' => 'Urgent',
    ];

    public const QUALIFICATION_LABELS = [
        'pending'   => 'Pending Review',
        'approved'  => 'Approved',
        'rejected'  => 'Rejected',
        'need_info' => 'Need More Info',
    ];

    public const QUALIFICATION_COLORS = [
        'pending'   => '#f59e0b',
        'approved'  => '#22c55e',
        'rejected'  => '#ef4444',
        'need_info' => '#6366f1',
    ];

    public const RISK_LABELS = [
        'low'    => '🟢 Low Risk',
        'medium' => '🟡 Medium Risk',
        'high'   => '🔴 High Risk',
    ];

    // ── Relationships ──
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function proposals()
    {
        return $this->hasMany(CrmProposal::class)->orderByDesc('version');
    }

    public function latestProposal()
    {
        return $this->hasOne(CrmProposal::class)->latestOfMany('version');
    }

    public function activities()
    {
        return $this->hasMany(CrmActivity::class, 'entity_id')
            ->where('entity_type', 'deal')
            ->orderByDesc('created_at');
    }

    public function followUps()
    {
        return $this->hasMany(CrmFollowUp::class, 'entity_id')
            ->where('entity_type', 'deal')
            ->orderBy('scheduled_at');
    }

    // ── Conversion ──

    /**
     * Convert this deal into a Client (when closed_won).
     */
    public function convertToClient(?int $teamId = null): Client
    {
        $client = Client::create([
            'name'              => $this->company ?: $this->contact_name ?: $this->title,
            'email'             => $this->contact_email,
            'phone'             => $this->contact_phone,
            'institution_type'  => $this->lead?->institute_type ?? $this->lead?->demoRequest?->institution_type ?? null,
            'district'          => $this->lead?->demoRequest?->district ?? null,
            'principal_name'    => $this->contact_name ?? $this->decision_maker_name,
            'principal_phone'   => $this->contact_phone,
            'contract_start'    => now(),
            'is_active'         => true,
            'is_live'           => false,
            'is_featured'       => false,
            'lead_id'           => $this->lead_id,
            'deal_id'           => $this->id,
            'billing_type'      => 'prepaid',
            'payment_frequency' => 'monthly',
            'package_price'     => $this->latestProposal?->final_price ?? $this->value,
            'billing_status'    => 'active',
            'activation_status' => 'pending',
            'implementation_status' => 'not_started',
            'client_priority'   => 'standard',
            'pipeline_stage'    => $this->previous_software_used ? 'migration' : 'training',
        ]);

        // Attach team
        if ($teamId || $this->team_id) {
            $client->teams()->attach($teamId ?: $this->team_id);
        }

        // Link deal to client
        $this->update([
            'client_id'      => $client->id,
            'stage'          => 'closed_won',
            'pipeline_stage' => 'deal_won',
            'closed_at'      => now(),
            'probability'    => 100,
        ]);

        // Link lead to client
        if ($this->lead_id) {
            Lead::where('id', $this->lead_id)->update(['client_id' => $client->id]);
        }

        // Auto-create Migration if previous software
        if ($this->previous_software_used) {
            $migration = CrmMigration::create([
                'client_id'              => $client->id,
                'deal_id'                => $this->id,
                'assigned_to'            => $this->assigned_to ?? auth()->id(),
                'previous_software_name' => $this->previous_software_name ?? 'Unknown',
                'data_categories'        => CrmMigration::DEFAULT_DATA_CATEGORIES,
                'data_collection_method' => 'manual',
                'migration_start_date'   => now()->addDays(1),
                'migration_end_date'     => now()->addDays(30),
                'buffer_days'            => 5,
                'old_system_status'      => 'running',
                'status'                 => 'not_started',
            ]);

            $migration->generateDefaultTasks();

            CrmActivity::log('client', $client->id, 'system',
                'Legacy System Migration created',
                "Previous software: {$this->previous_software_name}. 15 tasks auto-generated."
            );
        }

        // Auto-create Training
        CrmTraining::create([
            'client_id'     => $client->id,
            'title'         => 'Initial ERP Training — ' . $client->name,
            'training_type' => 'online',
            'modules'       => $this->modules_required,
            'trainer_id'    => $this->assigned_to ?? auth()->id(),
            'start_date'    => $this->previous_software_used ? now()->addDays(35) : now()->addDays(3),
            'end_date'      => $this->previous_software_used ? now()->addDays(65) : now()->addDays(33),
            'status'        => 'scheduled',
            'total_sessions' => 10,
        ]);

        // Auto-create Billing Plan
        $proposal = $this->latestProposal;
        CrmBillingPlan::create([
            'client_id'        => $client->id,
            'plan_name'        => 'ERP Package — ' . $client->name,
            'billing_type'     => 'prepaid',
            'frequency'        => 'monthly',
            'base_amount'      => $proposal?->final_price ?? $this->value ?? 0,
            'addons'           => null,
            'total_amount'     => $proposal?->final_price ?? $this->value ?? 0,
            'start_date'       => now(),
            'next_billing_date' => now()->addMonth(),
            'is_active'        => true,
            'auto_renew'       => true,
        ]);

        // Log activities
        CrmActivity::log('deal', $this->id, 'conversion',
            "Deal Won! Client #{$client->id} created", $client->name
        );
        CrmActivity::log('client', $client->id, 'system',
            "Client created from Deal #{$this->id}",
            "Training scheduled, Billing plan created" . ($this->previous_software_used ? ', Migration initiated' : '')
        );

        return $client;
    }

    // ── Helpers ──
    public function getStageProgressAttribute(): int
    {
        $stages = self::KANBAN_STAGES;
        $index = array_search($this->stage, $stages);
        if ($index === false) {
            return $this->stage === 'closed_won' ? 100 : 0;
        }
        return (int) round(($index + 1) / count($stages) * 100);
    }

    public function isConverted(): bool
    {
        return $this->stage === 'closed_won' && $this->client_id !== null;
    }

    // ── Scopes ──

    public function scopeByPipelineStage($query, string $stage)
    {
        return $query->where('pipeline_stage', $stage);
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('pipeline_stage', ['deal_won', 'lost']);
    }

    public function scopeWon($query)
    {
        return $query->where('pipeline_stage', 'deal_won');
    }
}
