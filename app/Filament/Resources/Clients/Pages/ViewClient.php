<?php

namespace App\Filament\Resources\Clients\Pages;

use App\Filament\Resources\Clients\ClientResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Schema;

class ViewClient extends ViewRecord
{
    protected static string $resource = ClientResource::class;

    protected string $view = 'filament.resources.clients.pages.view-client';

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->record->name ?? 'View Client';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->icon('heroicon-o-pencil-square'),
        ];
    }

    public function infolist(Schema $schema): Schema
    {
        return $schema;
    }
}
