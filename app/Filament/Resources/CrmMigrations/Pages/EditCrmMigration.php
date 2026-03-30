<?php

namespace App\Filament\Resources\CrmMigrations\Pages;

use App\Filament\Resources\CrmMigrations\CrmMigrationResource;
use Filament\Resources\Pages\EditRecord;

class EditCrmMigration extends EditRecord
{
    protected static string $resource = CrmMigrationResource::class;
}
