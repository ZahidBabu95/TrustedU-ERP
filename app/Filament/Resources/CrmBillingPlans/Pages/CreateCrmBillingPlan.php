<?php

namespace App\Filament\Resources\CrmBillingPlans\Pages;

use App\Filament\Resources\CrmBillingPlans\CrmBillingPlanResource;
use App\Models\CrmActivity;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmBillingPlan extends CreateRecord
{
    protected static string $resource = CrmBillingPlanResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Calculate total
        $addonsTotal = collect($data['addons'] ?? [])->sum('price');
        $data['total_amount'] = round(floatval($data['base_amount'] ?? 0) + $addonsTotal, 2);
        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;
        CrmActivity::log('client', $record->client_id, 'note',
            "Billing plan created: {$record->plan_name}",
            "Amount: ৳{$record->total_amount}, Frequency: {$record->frequency}"
        );
    }
}
