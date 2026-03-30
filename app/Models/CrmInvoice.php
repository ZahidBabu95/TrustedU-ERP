<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmInvoice extends Model
{
    protected $guarded = [];

    protected $casts = [
        'items'           => 'array',
        'subtotal'        => 'decimal:2',
        'tax_percent'     => 'decimal:2',
        'tax_amount'      => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total'           => 'decimal:2',
        'paid_amount'     => 'decimal:2',
        'issue_date'      => 'date',
        'due_date'        => 'date',
        'paid_at'         => 'datetime',
    ];

    public const STATUS_LABELS = [
        'draft'          => 'Draft',
        'sent'           => 'Sent',
        'paid'           => 'Paid',
        'partially_paid' => 'Partially Paid',
        'overdue'        => 'Overdue',
        'cancelled'      => 'Cancelled',
    ];

    public const STATUS_COLORS = [
        'draft'          => '#94a3b8',
        'sent'           => '#3b82f6',
        'paid'           => '#22c55e',
        'partially_paid' => '#f59e0b',
        'overdue'        => '#ef4444',
        'cancelled'      => '#6b7280',
    ];

    public const PAYMENT_METHOD_LABELS = [
        'bank_transfer' => '🏦 Bank Transfer',
        'bkash'         => '📱 bKash',
        'nagad'         => '📱 Nagad',
        'rocket'        => '📱 Rocket',
        'cash'          => '💵 Cash',
        'cheque'        => '📝 Cheque',
        'online'        => '💳 Online Payment',
    ];

    protected static function booted(): void
    {
        static::creating(function (CrmInvoice $invoice) {
            if (empty($invoice->invoice_number)) {
                $year = now()->format('Y');
                $lastNum = static::where('invoice_number', 'like', "INV-{$year}-%")->count();
                $invoice->invoice_number = "INV-{$year}-" . str_pad($lastNum + 1, 4, '0', STR_PAD_LEFT);
            }
            if (empty($invoice->created_by)) {
                $invoice->created_by = auth()->id();
            }
        });
    }

    // ── Relationships ──

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function billingPlan(): BelongsTo
    {
        return $this->belongsTo(CrmBillingPlan::class, 'billing_plan_id');
    }

    public function migration(): BelongsTo
    {
        return $this->belongsTo(CrmMigration::class, 'migration_id');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CrmPayment::class, 'invoice_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Helpers ──

    public function calculateTotals(): void
    {
        $subtotal    = collect($this->items ?? [])->sum(fn ($item) => ($item['qty'] ?? 1) * ($item['rate'] ?? $item['amount'] ?? 0));
        $taxAmount   = $subtotal * ($this->tax_percent / 100);
        $total       = $subtotal + $taxAmount - ($this->discount_amount ?? 0);

        $this->update([
            'subtotal'   => $subtotal,
            'tax_amount' => $taxAmount,
            'total'      => max(0, $total),
        ]);
    }

    public function getDueAmountAttribute(): float
    {
        return max(0, $this->total - $this->paid_amount);
    }

    public function isFullyPaid(): bool
    {
        return $this->paid_amount >= $this->total;
    }

    public function recordPayment(float $amount): void
    {
        $newPaid = $this->paid_amount + $amount;

        $this->update([
            'paid_amount' => $newPaid,
            'status'      => $newPaid >= $this->total ? 'paid' : 'partially_paid',
            'paid_at'     => $newPaid >= $this->total ? now() : $this->paid_at,
        ]);
    }

    public function getAmountInWordsAttribute(): string
    {
        $total = (int) $this->total;
        if ($total <= 0) return 'Zero';

        $units = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten',
            'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen',
            'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $convert = function ($num) use (&$convert, $units, $tens) {
            if ($num < 20) return $units[$num];
            if ($num < 100) return $tens[(int)($num / 10)] . ($num % 10 ? ' ' . $units[$num % 10] : '');
            if ($num < 1000) return $units[(int)($num / 100)] . ' Hundred' . ($num % 100 ? ' ' . $convert($num % 100) : '');
            if ($num < 100000) return $convert((int)($num / 1000)) . ' Thousand' . ($num % 1000 ? ' ' . $convert($num % 1000) : '');
            if ($num < 10000000) return $convert((int)($num / 100000)) . ' Lakh' . ($num % 100000 ? ' ' . $convert($num % 100000) : '');
            return $convert((int)($num / 10000000)) . ' Crore' . ($num % 10000000 ? ' ' . $convert($num % 10000000) : '');
        };

        return $convert($total) . ' Taka Only';
    }

    // ── Scopes ──

    public function scopeOverdue($query)
    {
        return $query->where('due_date', '<', now())
            ->whereNotIn('status', ['paid', 'cancelled']);
    }

    public function scopeUnpaid($query)
    {
        return $query->whereIn('status', ['sent', 'partially_paid', 'overdue']);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('issue_date', now()->month)
            ->whereYear('issue_date', now()->year);
    }
}
