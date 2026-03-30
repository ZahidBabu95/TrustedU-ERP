<?php

namespace App\Filament\Resources\CrmInvoices\Pages;

use App\Filament\Resources\CrmInvoices\CrmInvoiceResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCrmInvoice extends ViewRecord
{
    protected static string $resource = CrmInvoiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
