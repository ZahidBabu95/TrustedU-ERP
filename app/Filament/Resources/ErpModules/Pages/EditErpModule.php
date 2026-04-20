<?php

namespace App\Filament\Resources\ErpModules\Pages;

use App\Filament\Resources\ErpModules\ErpModuleResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\Action;
use Filament\Resources\Pages\EditRecord;

class EditErpModule extends EditRecord
{
    protected static string $resource = ErpModuleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('👁️ Preview Page')
                ->url(fn ($record) => route('module.show', $record->slug))
                ->openUrlInNewTab()
                ->color('info'),
            DeleteAction::make(),
        ];
    }
}
