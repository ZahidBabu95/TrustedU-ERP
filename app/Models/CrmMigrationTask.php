<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmMigrationTask extends Model
{
    protected $guarded = [];

    protected $casts = [
        'due_date'     => 'date',
        'completed_at' => 'datetime',
    ];

    public const CATEGORY_LABELS = [
        'onboarding'       => '📋 Onboarding',
        'data_processing'  => '⚙️ Data Processing',
        'system_entry'     => '💻 System Entry',
        'verification'     => '✅ Verification',
        'training'         => '🎓 Training',
        'handover'         => '🤝 Handover',
        'invoice'          => '📄 Invoice & Deed',
    ];

    public const STATUS_LABELS = [
        'pending'     => 'Pending',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'blocked'     => 'Blocked',
        'skipped'     => 'Skipped',
    ];

    public const STATUS_COLORS = [
        'pending'     => '#94a3b8',
        'in_progress' => '#3b82f6',
        'completed'   => '#22c55e',
        'blocked'     => '#ef4444',
        'skipped'     => '#6b7280',
    ];

    public const PRIORITY_LABELS = [
        'low'      => 'Low',
        'medium'   => 'Medium',
        'high'     => 'High',
        'critical' => 'Critical',
    ];

    public const PRIORITY_COLORS = [
        'low'      => '#94a3b8',
        'medium'   => '#3b82f6',
        'high'     => '#f97316',
        'critical' => '#ef4444',
    ];

    // ── Relationships ──

    public function migration(): BelongsTo
    {
        return $this->belongsTo(CrmMigration::class, 'migration_id');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function completedByUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // ── Helpers ──

    public function markCompleted(): void
    {
        $this->update([
            'status'       => 'completed',
            'completed_at' => now(),
            'completed_by' => auth()->id(),
        ]);

        // Update migration progress
        $this->migration->updateProgress();

        // Log activity
        CrmActivity::log(
            'migration',
            $this->migration_id,
            'task_complete',
            "Task completed: {$this->title}",
            null,
            ['task_id' => $this->id, 'task_category' => $this->task_category],
        );
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCritical($query)
    {
        return $query->where('priority', 'critical');
    }
}
