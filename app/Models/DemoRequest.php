<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DemoRequest extends Model
{
    protected $fillable = [
        'contact_name', 'email', 'phone', 'institution_name',
        'institution_type', 'district', 'student_count',
        'interested_modules', 'preferred_date', 'notes',
        'status', 'assigned_to', 'source',
    ];

    protected $casts = [
        'interested_modules' => 'array',
        'preferred_date'     => 'date',
    ];

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function scopePending(Builder $query): Builder
    {
        return $query->where('status', 'pending');
    }

    public function scopeRecent(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }
}
