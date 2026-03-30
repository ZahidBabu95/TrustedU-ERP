<?php

namespace App\Filament\Resources\CrmFollowUps\Pages;

use App\Filament\Resources\CrmFollowUps\CrmFollowUpResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmFollowUps extends ListRecords
{
    protected static string $resource = CrmFollowUpResource::class;
    protected static ?string $title = 'Follow-ups';
}
