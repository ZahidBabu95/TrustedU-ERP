<?php

namespace App\Models;

use App\Models\Traits\HasTeamScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes, HasTeamScope;

    protected $guarded = [];

    protected $casts = [
        'resolved_at'      => 'datetime',
        'last_reply_at'    => 'datetime',
        'closed_at'        => 'datetime',
        'labels'           => 'array',
        'notify_requester' => 'boolean',
    ];

    // ── Constants ──

    public const STATUS_LABELS = [
        'open'        => 'Open',
        'in_progress' => 'In Progress',
        'resolved'    => 'Resolved',
        'closed'      => 'Closed',
    ];

    public const STATUS_COLORS = [
        'open'        => '#3b82f6',
        'in_progress' => '#eab308',
        'resolved'    => '#22c55e',
        'closed'      => '#94a3b8',
    ];

    public const PRIORITY_LABELS = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    public const PRIORITY_COLORS = [
        'low'    => '#94a3b8',
        'medium' => '#3b82f6',
        'high'   => '#f97316',
        'urgent' => '#ef4444',
    ];

    public const CATEGORY_LABELS = [
        'technical'  => 'Technical Support',
        'billing'    => 'Billing & Invoices',
        'access'     => 'Account Access',
        'feature'    => 'Feature Request',
        'bug'        => 'Bug Report',
        'general'    => 'General Inquiry',
        'onboarding' => 'Onboarding Help',
    ];

    // ── Boot ──

    protected static function booted(): void
    {
        static::creating(function (SupportTicket $ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . strtoupper(Str::random(6));
            }
            if (empty($ticket->assigned_to) && auth()->check()) {
                $ticket->assigned_to = auth()->id();
            }
        });
    }

    // ── Relationships ──

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(SupportMessage::class, 'ticket_id')->orderBy('created_at');
    }

    // ── Helpers ──

    public function addMessage(string $message, ?int $senderId = null, string $senderType = 'agent', ?string $attachment = null, bool $isInternal = false): SupportMessage
    {
        $msg = $this->messages()->create([
            'sender_id'   => $senderId ?? auth()->id(),
            'sender_type' => $senderType,
            'message'     => $message,
            'attachment'  => $attachment,
            'is_internal' => $isInternal,
        ]);

        $this->update(['last_reply_at' => now()]);

        // Auto update status on agent reply
        if ($senderType === 'agent' && $this->status === 'open') {
            $this->update(['status' => 'in_progress']);
        }

        return $msg;
    }

    public function resolve(): void
    {
        $this->update([
            'status'      => 'resolved',
            'resolved_at' => now(),
        ]);
    }

    public function close(): void
    {
        $this->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);
    }

    public function reopen(): void
    {
        $this->update([
            'status'      => 'open',
            'resolved_at' => null,
            'closed_at'   => null,
        ]);
    }

    // ── Scopes ──

    public function scopeOpen($query)
    {
        return $query->where('status', 'open');
    }

    public function scopeUnresolved($query)
    {
        return $query->whereIn('status', ['open', 'in_progress']);
    }

    // ── Accessors ──

    public function getPriorityColorAttribute(): string
    {
        return self::PRIORITY_COLORS[$this->priority] ?? '#94a3b8';
    }

    public function getStatusColorAttribute(): string
    {
        return self::STATUS_COLORS[$this->status] ?? '#94a3b8';
    }
}
