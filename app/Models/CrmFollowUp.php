<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmFollowUp extends Model
{
    protected $guarded = [];

    protected $casts = [
        'scheduled_at'      => 'datetime',
        'completed_at'      => 'datetime',
        'next_follow_up_at' => 'datetime',
    ];

    public const TYPE_LABELS = [
        'call'     => '📞 Phone Call',
        'meeting'  => '🤝 Meeting',
        'email'    => '📧 Email',
        'task'     => '📋 Task',
        'reminder' => '🔔 Reminder',
        'demo'     => '💻 Demo',
    ];

    public const STATUS_LABELS = [
        'pending'     => 'Pending',
        'completed'   => 'Completed',
        'missed'      => 'Missed',
        'cancelled'   => 'Cancelled',
        'rescheduled' => 'Rescheduled',
    ];

    public const STATUS_COLORS = [
        'pending'     => '#f59e0b',
        'completed'   => '#22c55e',
        'missed'      => '#ef4444',
        'cancelled'   => '#94a3b8',
        'rescheduled' => '#6366f1',
    ];

    public const PRIORITY_LABELS = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    // ── Relationships ──

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function entity()
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }

    // ── Scopes ──

    public function scopeForEntity($query, string $type, int $id)
    {
        return $query->where('entity_type', $type)->where('entity_id', $id);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>=', now())
            ->where('status', 'pending')
            ->orderBy('scheduled_at');
    }

    public function scopeOverdue($query)
    {
        return $query->where('scheduled_at', '<', now())
            ->where('status', 'pending');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // ── Helpers ──

    public function markCompleted(?string $outcome = null): void
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'outcome'      => $outcome,
        ]);

        CrmActivity::log(
            $this->entity_type,
            $this->entity_id,
            'follow_up',
            "Follow-up completed: {$this->title}",
            $outcome,
        );
    }

    public function markMissed(): void
    {
        $this->update(['status' => 'missed']);
    }
}
