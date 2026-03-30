<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmProposal extends Model
{
    protected $guarded = [];

    protected $casts = [
        'modules_included' => 'array',
        'base_price'       => 'decimal:2',
        'discount_percent' => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'final_price'      => 'decimal:2',
        'sent_at'          => 'datetime',
        'approved_at'      => 'datetime',
        'rejected_at'      => 'datetime',
    ];

    public const STATUS_LABELS = [
        'draft'       => 'Draft',
        'sent'        => 'Sent',
        'negotiation' => 'Negotiation',
        'approved'    => 'Approved',
        'rejected'    => 'Rejected',
        'expired'     => 'Expired',
    ];

    public const STATUS_COLORS = [
        'draft'       => '#94a3b8',
        'sent'        => '#3b82f6',
        'negotiation' => '#f59e0b',
        'approved'    => '#22c55e',
        'rejected'    => '#ef4444',
        'expired'     => '#6b7280',
    ];

    // ── Relationships ──

    public function lead(): BelongsTo
    {
        return $this->belongsTo(\App\Models\Lead::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ──

    public function calculateFinalPrice(): void
    {
        $discountAmt = $this->discount_percent > 0
            ? ($this->base_price * $this->discount_percent / 100)
            : $this->discount_amount;

        $this->update([
            'discount_amount' => $discountAmt,
            'final_price'     => max(0, $this->base_price - $discountAmt),
        ]);
    }

    public function createNewVersion(): self
    {
        $query = static::query();
        if ($this->lead_id) {
            $query->where('lead_id', $this->lead_id);
        } elseif ($this->deal_id) {
            $query->where('deal_id', $this->deal_id);
        }
        $maxVersion = $query->max('version') ?? 0;

        return static::create([
            'lead_id'          => $this->lead_id,
            'deal_id'          => $this->deal_id,
            'version'          => $maxVersion + 1,
            'title'            => $this->title,
            'modules_included' => $this->modules_included,
            'base_price'       => $this->base_price,
            'discount_percent' => $this->discount_percent,
            'discount_amount'  => $this->discount_amount,
            'final_price'      => $this->final_price,
            'implementation_days' => $this->implementation_days,
            'payment_terms'    => $this->payment_terms,
            'validity_days'    => $this->validity_days,
            'status'           => 'draft',
            'notes'            => "Revised from v{$this->version}",
            'created_by'       => auth()->id(),
        ]);
    }

    public function markSent(): void
    {
        $this->update(['status' => 'sent', 'sent_at' => now()]);
    }

    public function markApproved(): void
    {
        $this->update(['status' => 'approved', 'approved_at' => now()]);
    }

    // ── Scopes ──

    public function scopeLatestVersion($query)
    {
        return $query->orderByDesc('version');
    }

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['rejected', 'expired']);
    }
}
