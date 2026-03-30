<?php

namespace App\Filament\Resources\CrmTrainings\Pages;

use App\Filament\Resources\CrmTrainings\CrmTrainingResource;
use App\Models\CrmActivity;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmTraining extends CreateRecord
{
    protected static string $resource = CrmTrainingResource::class;

    protected function afterCreate(): void
    {
        $record = $this->record;

        CrmActivity::log('client', $record->client_id, 'note',
            "Training scheduled: {$record->title}",
            "Type: " . ($record->training_type) .
            ", Sessions: {$record->total_sessions}",
            ['training_id' => $record->id]
        );
    }
}
