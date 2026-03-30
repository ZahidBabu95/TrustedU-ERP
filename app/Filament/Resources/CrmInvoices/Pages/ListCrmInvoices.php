<?php

namespace App\Filament\Resources\CrmInvoices\Pages;

use App\Filament\Resources\CrmInvoices\CrmInvoiceResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmInvoices extends ListRecords
{
    protected static string $resource = CrmInvoiceResource::class;
    protected static ?string $title = 'Invoices';
}
