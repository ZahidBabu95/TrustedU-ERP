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
        'value' => 'decimal:2',
        'expected_close_date' => 'date',
    ];

    // ── Status constants ──
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
        'web'       => 'Inbound',
        'referral'  => 'Referral',
        'social'    => 'Social',
        'cold_call' => 'Cold Call',
        'email'     => 'Email',
        'other'     => 'Other',
    ];

    public const PRIORITY_LABELS = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High Priority',
        'urgent' => 'Urgent',
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

    // ── Conversion ──

    /**
     * Convert this lead into a CRM Deal.
     */
    public function convertToDeal(?int $assignedTo = null): Deal
    {
        $deal = Deal::create([
            'title'              => ($this->company ?: $this->name) . ' — Deal',
            'company'            => $this->company,
            'contact_name'       => $this->name,
            'contact_email'      => $this->email,
            'contact_phone'      => $this->phone,
            'value'              => $this->value,
            'stage'              => 'discovery',
            'priority'           => $this->priority ?? 'medium',
            'deal_source'        => 'lead',
            'probability'        => 10,
            'lead_id'            => $this->id,
            'assigned_to'        => $assignedTo ?? $this->assigned_to,
            'team_id'            => $this->team_id,
            'expected_close_date' => $this->expected_close_date,
            'notes'              => "Converted from Lead #{$this->id}: {$this->name}\n" . ($this->notes ?? ''),
        ]);

        $this->update(['status' => 'won']);

        return $deal;
    }

    // ── Helpers ──

    public function isConverted(): bool
    {
        return $this->status === 'won' && $this->deals()->exists();
    }
}
