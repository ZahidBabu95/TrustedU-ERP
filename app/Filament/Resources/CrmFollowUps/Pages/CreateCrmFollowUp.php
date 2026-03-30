<?php

namespace App\Filament\Resources\CrmFollowUps\Pages;

use App\Filament\Resources\CrmFollowUps\CrmFollowUpResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmFollowUp extends CreateRecord
{
    protected static string $resource = CrmFollowUpResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();
        $data['status'] = 'pending';
        return $data;
    }
}
