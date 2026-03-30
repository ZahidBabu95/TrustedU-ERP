<?php

namespace App\Filament\Resources\CrmBillingPlans\Pages;

use App\Filament\Resources\CrmBillingPlans\CrmBillingPlanResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmBillingPlans extends ListRecords
{
    protected static string $resource = CrmBillingPlanResource::class;
    protected static ?string $title = 'Billing Plans';
}
