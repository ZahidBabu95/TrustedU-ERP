<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmBillingPlan extends Model
{
    protected $guarded = [];

    protected $casts = [
        'base_amount'   => 'decimal:2',
        'total_amount'  => 'decimal:2',
        'addons'        => 'array',
        'start_date'    => 'date',
        'end_date'      => 'date',
        'next_billing_date' => 'date',
        'is_active'     => 'boolean',
        'auto_renew'    => 'boolean',
    ];

    public const BILLING_TYPE_LABELS = [
        'prepaid'  => 'Prepaid',
        'postpaid' => 'Postpaid',
    ];

    public const FREQUENCY_LABELS = [
        'monthly'     => 'Monthly',
        'quarterly'   => 'Quarterly',
        'half_yearly' => 'Half Yearly',
        'yearly'      => 'Yearly',
    ];

    public const FREQUENCY_MONTHS = [
        'monthly'     => 1,
        'quarterly'   => 3,
        'half_yearly' => 6,
        'yearly'      => 12,
    ];

    // ── Relationships ──

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(CrmInvoice::class, 'billing_plan_id');
    }

    // ── Helpers ──

    public function calculateTotal(): void
    {
        $addonsTotal = collect($this->addons ?? [])->sum('price');
        $this->update(['total_amount' => $this->base_amount + $addonsTotal]);
    }

    public function getNextBillingDate(): \Carbon\Carbon
    {
        $months = self::FREQUENCY_MONTHS[$this->frequency] ?? 1;
        return $this->next_billing_date->addMonths($months);
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueSoon($query, int $days = 7)
    {
        return $query->where('is_active', true)
            ->where('next_billing_date', '<=', now()->addDays($days));
    }
}
