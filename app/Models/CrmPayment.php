<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CrmPayment extends Model
{
    protected $guarded = [];

    protected $casts = [
        'amount'       => 'decimal:2',
        'payment_date' => 'date',
    ];

    public const METHOD_LABELS = [
        'bank_transfer' => '🏦 Bank Transfer',
        'bkash'         => '📱 bKash',
        'nagad'         => '📱 Nagad',
        'rocket'        => '📱 Rocket',
        'cash'          => '💵 Cash',
        'cheque'        => '📝 Cheque',
        'card'          => '💳 Card',
        'other'         => '📋 Other',
    ];

    // ── Relationships ──

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(CrmInvoice::class, 'invoice_id');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    // ── Boot ──

    protected static function booted(): void
    {
        static::created(function (CrmPayment $payment) {
            // Auto-update invoice paid amount
            $payment->invoice->recordPayment($payment->amount);

            // Log activity
            CrmActivity::log(
                'client',
                $payment->client_id,
                'note',
                "Payment received: ৳{$payment->amount}",
                "Invoice: {$payment->invoice->invoice_number}, Method: " . (self::METHOD_LABELS[$payment->payment_method] ?? $payment->payment_method),
                ['invoice_id' => $payment->invoice_id, 'amount' => $payment->amount],
            );
        });
    }

    // ── Scopes ──

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('payment_date', now()->month)
            ->whereYear('payment_date', now()->year);
    }
}
