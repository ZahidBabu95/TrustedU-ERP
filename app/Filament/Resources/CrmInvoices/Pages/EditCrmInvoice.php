<?php

namespace App\Filament\Resources\CrmInvoices\Pages;

use App\Filament\Resources\CrmInvoices\CrmInvoiceResource;
use Filament\Resources\Pages\EditRecord;

class EditCrmInvoice extends EditRecord
{
    protected static string $resource = CrmInvoiceResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Recalculate totals on save
        $subtotal  = collect($data['items'] ?? [])->sum(fn ($item) => ($item['qty'] ?? 1) * ($item['rate'] ?? $item['amount'] ?? 0));
        $taxAmount = $subtotal * (floatval($data['tax_percent'] ?? 0) / 100);
        $discount  = floatval($data['discount_amount'] ?? 0);
        $total     = max(0, $subtotal + $taxAmount - $discount);

        $data['subtotal']   = round($subtotal, 2);
        $data['tax_amount'] = round($taxAmount, 2);
        $data['total']      = round($total, 2);

        return $data;
    }
}
