<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class CrmActivity extends Model
{
    protected $guarded = [];

    protected $casts = [
        'metadata'  => 'array',
        'is_pinned' => 'boolean',
    ];

    public const TYPE_NOTE          = 'note';
    public const TYPE_CALL          = 'call';
    public const TYPE_MEETING       = 'meeting';
    public const TYPE_EMAIL         = 'email';
    public const TYPE_SMS           = 'sms';
    public const TYPE_STAGE_CHANGE  = 'stage_change';
    public const TYPE_FILE_UPLOAD   = 'file_upload';
    public const TYPE_STATUS_CHANGE = 'status_change';
    public const TYPE_TASK_COMPLETE = 'task_complete';
    public const TYPE_FOLLOW_UP     = 'follow_up';
    public const TYPE_CONVERSION    = 'conversion';
    public const TYPE_SYSTEM        = 'system';

    public const TYPE_LABELS = [
        'note'          => '📝 Note',
        'call'          => '📞 Call',
        'meeting'       => '🤝 Meeting',
        'email'         => '📧 Email',
        'sms'           => '💬 SMS',
        'stage_change'  => '🔀 Stage Change',
        'file_upload'   => '📎 File Upload',
        'status_change' => '🔄 Status Change',
        'task_complete' => '✅ Task Complete',
        'follow_up'     => '📅 Follow-up',
        'conversion'    => '🎯 Conversion',
        'system'        => '⚙️ System',
    ];

    public const TYPE_COLORS = [
        'note'          => '#6366f1',
        'call'          => '#3b82f6',
        'meeting'       => '#10b981',
        'email'         => '#8b5cf6',
        'sms'           => '#f59e0b',
        'stage_change'  => '#f97316',
        'file_upload'   => '#64748b',
        'status_change' => '#ec4899',
        'task_complete' => '#22c55e',
        'follow_up'     => '#14b8a6',
        'conversion'    => '#eab308',
        'system'        => '#94a3b8',
    ];

    // ── Relationships ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function entity()
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }

    // ── Helpers ──

    public static function log(
        string $entityType,
        int $entityId,
        string $type,
        string $title,
        ?string $description = null,
        ?array $metadata = null,
        ?int $userId = null,
    ): self {
        return static::create([
            'entity_type' => $entityType,
            'entity_id'   => $entityId,
            'user_id'     => $userId ?? auth()->id() ?? 1,
            'type'        => $type,
            'title'       => $title,
            'description' => $description,
            'metadata'    => $metadata,
        ]);
    }

    // ── Scopes ──

    public function scopeForEntity($query, string $type, int $id)
    {
        return $query->where('entity_type', $type)->where('entity_id', $id);
    }

    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }
}
