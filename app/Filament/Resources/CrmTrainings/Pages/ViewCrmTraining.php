<?php

namespace App\Filament\Resources\CrmTrainings\Pages;

use App\Filament\Resources\CrmTrainings\CrmTrainingResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewCrmTraining extends ViewRecord
{
    protected static string $resource = CrmTrainingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
