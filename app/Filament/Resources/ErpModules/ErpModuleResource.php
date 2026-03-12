<?php

namespace App\Filament\Resources\ErpModules;

use App\Filament\Resources\ErpModules\Pages\CreateErpModule;
use App\Filament\Resources\ErpModules\Pages\EditErpModule;
use App\Filament\Resources\ErpModules\Pages\ListErpModules;
use App\Filament\Resources\ErpModules\Schemas\ErpModuleForm;
use App\Filament\Resources\ErpModules\Tables\ErpModulesTable;
use App\Models\ErpModule;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ErpModuleResource extends Resource
{
    protected static ?string $model = ErpModule::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return \Filament\Support\Icons\Heroicon::OutlinedSquares2x2; }
    public static function getNavigationGroup(): ?string { return 'Landing Page'; }

    public static function form(Schema $schema): Schema
    {
        return ErpModuleForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ErpModulesTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListErpModules::route('/'),
            'create' => CreateErpModule::route('/create'),
            'edit' => EditErpModule::route('/{record}/edit'),
        ];
    }
}
