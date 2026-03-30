<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkOrder extends Model
{
    protected $guarded = [];

    protected $casts = [
        'items'             => 'array',
        'total_amount'      => 'decimal:2',
        'start_date'        => 'date',
        'expected_delivery' => 'date',
    ];

    public const STATUS_LABELS = [
        'generated'   => 'Generated',
        'in_progress' => 'In Progress',
        'completed'   => 'Completed',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function generator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'generated_by');
    }

    /**
     * Generate a unique order number like WO-2026-0001
     */
    public static function generateOrderNumber(): string
    {
        $year = now()->format('Y');
        $last = static::where('order_number', 'like', "WO-{$year}-%")->max('order_number');
        $seq  = $last ? (int) substr($last, -4) + 1 : 1;
        return "WO-{$year}-" . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }
}
