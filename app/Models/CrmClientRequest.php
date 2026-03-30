<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class CrmClientRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_paid'        => 'boolean',
        'estimated_cost' => 'decimal:2',
        'completed_at'   => 'datetime',
    ];

    public const REQUEST_TYPE_LABELS = [
        'feature'       => '✨ New Feature',
        'update'        => '🔄 Update',
        'customization' => '🔧 Customization',
        'bug_fix'       => '🐛 Bug Fix',
    ];

    public const APPROVAL_STATUS_LABELS = [
        'pending'  => 'Pending',
        'approved' => 'Approved',
        'rejected' => 'Rejected',
        'on_hold'  => 'On Hold',
    ];

    public const APPROVAL_STATUS_COLORS = [
        'pending'  => '#f59e0b',
        'approved' => '#22c55e',
        'rejected' => '#ef4444',
        'on_hold'  => '#6366f1',
    ];

    public const IMPLEMENTATION_STATUS_LABELS = [
        'not_started'  => 'Not Started',
        'in_progress'  => 'In Progress',
        'testing'      => 'Testing',
        'completed'    => 'Completed',
    ];

    public const PRIORITY_LABELS = [
        'low'    => 'Low',
        'medium' => 'Medium',
        'high'   => 'High',
        'urgent' => 'Urgent',
    ];

    protected static function booted(): void
    {
        static::creating(function (CrmClientRequest $request) {
            if (empty($request->request_number)) {
                $year = now()->format('Y');
                $lastNum = static::where('request_number', 'like', "REQ-{$year}-%")->count();
                $request->request_number = "REQ-{$year}-" . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Relationships ──

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // ── Scopes ──

    public function scopePending($query)
    {
        return $query->where('approval_status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('approval_status', 'approved');
    }

    public function scopeForClient($query, int $clientId)
    {
        return $query->where('client_id', $clientId);
    }
}
