<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AppNotification extends Model
{
    use HasUuids;

    protected $table = 'notifications';

    protected $fillable = [
        'user_id', 'type', 'title', 'message', 'icon', 'color',
        'action_url', 'action_label', 'data', 'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    // ── Relationships ──

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ──

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    // ── Helpers ──

    public function markAsRead(): void
    {
        $this->update(['read_at' => now()]);
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    // ── Static Creators ──

    public static function send(
        int $userId,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null,
        ?string $actionLabel = null,
        string $icon = 'heroicon-o-bell',
        string $color = 'primary',
        ?array $data = null,
    ): static {
        return static::create([
            'user_id'      => $userId,
            'type'         => $type,
            'title'        => $title,
            'message'      => $message,
            'icon'         => $icon,
            'color'        => $color,
            'action_url'   => $actionUrl,
            'action_label' => $actionLabel,
            'data'         => $data,
        ]);
    }

    public static function sendToMany(
        array $userIds,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null,
        string $icon = 'heroicon-o-bell',
        string $color = 'primary',
    ): void {
        foreach ($userIds as $userId) {
            static::send($userId, $type, $title, $message, $actionUrl, icon: $icon, color: $color);
        }
    }

    /**
     * Mark all unread notifications as read for a user.
     */
    public static function markAllRead(int $userId): int
    {
        return static::where('user_id', $userId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    /**
     * Get unread count for a user.
     */
    public static function unreadCount(int $userId): int
    {
        return static::where('user_id', $userId)->whereNull('read_at')->count();
    }
}
