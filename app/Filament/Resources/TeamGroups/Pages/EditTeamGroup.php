<?php

namespace App\Filament\Resources\TeamGroups\Pages;

use App\Filament\Resources\TeamGroups\TeamGroupResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamGroup extends EditRecord
{
    protected static string $resource = TeamGroupResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
