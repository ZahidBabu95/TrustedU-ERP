<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmTraining extends Model
{
    protected $guarded = [];

    protected $casts = [
        'modules'      => 'array',
        'attendees'    => 'array',
        'topics'       => 'array',
        'materials'    => 'array',
        'session_logs' => 'array',
        'start_date'   => 'date',
        'end_date'     => 'date',
        'session_time' => 'datetime:H:i',
    ];

    // ── Training Categories ──

    public const CATEGORY_LABELS = [
        'migration'      => '🔄 Migration Training',
        'onboarding'     => '📋 Onboarding Training',
        'module'         => '📦 Module Training',
        'advanced'       => '🚀 Advanced Training',
        'refresher'      => '🔁 Refresher Training',
        'troubleshoot'   => '🔧 Troubleshoot Session',
        'custom'         => '⚙️ Custom Training',
    ];

    // ── Training Types ──

    public const TYPE_LABELS = [
        'onsite'       => '🏢 সরাসরি (Onsite)',
        'online_zoom'  => '💻 Zoom Meeting',
        'online_meet'  => '📹 Google Meet',
        'video_call'   => '📱 Video Call',
        'phone'        => '📞 Phone Call',
        'hybrid'       => '🔀 Hybrid (Online + Onsite)',
    ];

    // ── Meeting Platforms ──

    public const PLATFORM_LABELS = [
        'zoom'         => '🟦 Zoom',
        'google_meet'  => '🟢 Google Meet',
        'teams'        => '🟣 Microsoft Teams',
        'whatsapp'     => '🟩 WhatsApp Video',
        'skype'        => '🔵 Skype',
        'other'        => '⬜ Other',
    ];

    // ── Status ──

    public const STATUS_LABELS = [
        'scheduled'   => 'Scheduled',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
        'cancelled'   => 'Cancelled',
        'postponed'   => 'Postponed',
    ];

    public const STATUS_COLORS = [
        'scheduled'   => '#3b82f6',
        'in_progress' => '#f59e0b',
        'completed'   => '#22c55e',
        'cancelled'   => '#94a3b8',
        'postponed'   => '#f97316',
    ];

    // ── Default Attendee Roles ──

    public const ATTENDEE_ROLE_LABELS = [
        'principal'   => '🏫 Principal / Head',
        'admin'       => '👤 Admin Staff',
        'teacher'     => '👩‍🏫 Teacher',
        'accountant'  => '💰 Accountant',
        'operator'    => '🖥️ Computer Operator',
        'other'       => '👥 Other',
    ];

    // ── Relationships ──

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'trainer_id');
    }

    public function migration(): BelongsTo
    {
        return $this->belongsTo(CrmMigration::class, 'migration_id');
    }

    // ── Helpers ──

    public function getProgressPercentAttribute(): int
    {
        if ($this->total_sessions <= 0) return 0;
        return (int) round(($this->completed_sessions / $this->total_sessions) * 100);
    }

    public function completeSession(?array $logData = null): void
    {
        $this->increment('completed_sessions');

        // Add session log
        if ($logData) {
            $logs = $this->session_logs ?? [];
            $logs[] = array_merge($logData, [
                'session_number' => $this->completed_sessions,
                'completed_at'   => now()->toDateTimeString(),
                'completed_by'   => auth()->user()?->name ?? 'System',
            ]);
            $this->update(['session_logs' => $logs]);
        }

        if ($this->completed_sessions >= $this->total_sessions) {
            $this->update(['status' => 'completed']);
        } elseif ($this->status === 'scheduled') {
            $this->update(['status' => 'in_progress']);
        }

        CrmActivity::log(
            'client',
            $this->client_id,
            'task_complete',
            "Training session completed: {$this->completed_sessions}/{$this->total_sessions}",
            "Training: {$this->title}",
        );
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['scheduled', 'in_progress']);
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('training_category', $category);
    }
}
