<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NegotiationLog extends Model
{
    protected $guarded = [];

    protected $casts = [
        'discussion_date' => 'datetime',
        'counter_offer'   => 'decimal:2',
    ];

    public const TYPE_LABELS = [
        'phone'      => '📞 Phone Call',
        'email'      => '📧 Email',
        'meeting'    => '🤝 In-Person Meeting',
        'video_call' => '💻 Video Call',
        'whatsapp'   => '💬 WhatsApp',
    ];

    public const RESPONSE_LABELS = [
        'positive' => '✅ Positive',
        'neutral'  => '⚖️ Neutral',
        'negative' => '❌ Negative',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function logger(): BelongsTo
    {
        return $this->belongsTo(User::class, 'logged_by');
    }
}
