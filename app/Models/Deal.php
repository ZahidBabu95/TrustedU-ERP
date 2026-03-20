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
        'value'               => 'decimal:2',
        'expected_close_date' => 'date',
        'closed_at'           => 'date',
    ];

    // ── Stage constants ──
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

    // ── Relationships ──
    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function assignee()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
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
}
