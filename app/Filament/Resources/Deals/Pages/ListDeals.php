<?php

namespace App\Filament\Resources\Deals\Pages;

use App\Filament\Resources\Deals\DealResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeals extends ListRecords
{
    protected static string $resource = DealResource::class;

    protected static ?string $title = 'Deed / Agreement';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('📜 নতুন Deed তৈরি করুন')
                ->icon('heroicon-o-document-check'),
        ];
    }
}
