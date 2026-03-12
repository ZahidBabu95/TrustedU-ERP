<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class ContactMessage extends Model
{
    protected $fillable = [
        'name', 'email', 'phone', 'subject',
        'message', 'ip_address', 'status',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->where('status', 'new');
    }

    public function markAsRead(): void
    {
        $this->update(['status' => 'read']);
    }
}
