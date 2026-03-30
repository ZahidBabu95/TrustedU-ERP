<?php

namespace App\Filament\Resources\CrmPayments\Pages;

use App\Filament\Resources\CrmPayments\CrmPaymentResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmPayments extends ListRecords
{
    protected static string $resource = CrmPaymentResource::class;
    protected static ?string $title = 'Payment Ledger';
}
