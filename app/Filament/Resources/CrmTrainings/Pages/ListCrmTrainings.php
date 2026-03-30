<?php

namespace App\Filament\Resources\CrmTrainings\Pages;

use App\Filament\Resources\CrmTrainings\CrmTrainingResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmTrainings extends ListRecords
{
    protected static string $resource = CrmTrainingResource::class;
    protected static ?string $title = 'Training Programs';
}
