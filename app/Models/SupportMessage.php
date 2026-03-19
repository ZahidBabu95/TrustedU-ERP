<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportMessage extends Model
{
    protected $fillable = [
        'ticket_id',
        'sender_id',
        'sender_type',
        'message',
        'attachment',
        'is_internal',
    ];

    protected $casts = [
        'is_internal' => 'boolean',
    ];

    // ── Relationships ──

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(SupportTicket::class, 'ticket_id');
    }

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // ── Helpers ──

    public function isFromAgent(): bool
    {
        return $this->sender_type === 'agent';
    }

    public function isFromClient(): bool
    {
        return $this->sender_type === 'client';
    }

    public function isSystemMessage(): bool
    {
        return $this->sender_type === 'system';
    }
}
