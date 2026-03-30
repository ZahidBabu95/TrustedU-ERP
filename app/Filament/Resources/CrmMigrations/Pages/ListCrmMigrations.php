<?php

namespace App\Filament\Resources\CrmMigrations\Pages;

use App\Filament\Resources\CrmMigrations\CrmMigrationResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmMigrations extends ListRecords
{
    protected static string $resource = CrmMigrationResource::class;
    protected static ?string $title = 'Data Migrations';
}
