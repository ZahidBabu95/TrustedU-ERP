<?php

namespace App\Filament\Resources\CrmInvoices\Pages;

use App\Filament\Resources\CrmInvoices\CrmInvoiceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmInvoice extends CreateRecord
{
    protected static string $resource = CrmInvoiceResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        // Calculate totals
        $subtotal    = collect($data['items'] ?? [])->sum(fn ($item) => ($item['qty'] ?? 1) * ($item['rate'] ?? $item['amount'] ?? 0));
        $taxAmount   = $subtotal * (floatval($data['tax_percent'] ?? 0) / 100);
        $discount    = floatval($data['discount_amount'] ?? 0);
        $total       = max(0, $subtotal + $taxAmount - $discount);

        $data['subtotal']   = round($subtotal, 2);
        $data['tax_amount'] = round($taxAmount, 2);
        $data['total']      = round($total, 2);
        $data['paid_amount'] = 0;

        return $data;
    }
}
