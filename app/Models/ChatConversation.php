<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatConversation extends Model
{
    protected $fillable = [
        'session_id', 'visitor_name', 'visitor_email', 'visitor_phone',
        'status', 'subject', 'summary', 'message_count',
        'last_message_at', 'ip_address', 'user_agent',
    ];

    protected $casts = [
        'last_message_at' => 'datetime',
    ];

    public function messages(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'conversation_id');
    }
}
