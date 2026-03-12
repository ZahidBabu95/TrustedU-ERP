<?php

namespace App\Filament\Resources\DemoRequests;

use App\Filament\Resources\DemoRequests\Pages\CreateDemoRequest;
use App\Filament\Resources\DemoRequests\Pages\EditDemoRequest;
use App\Filament\Resources\DemoRequests\Pages\ListDemoRequests;
use App\Filament\Resources\DemoRequests\Schemas\DemoRequestForm;
use App\Filament\Resources\DemoRequests\Tables\DemoRequestsTable;
use App\Models\DemoRequest;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class DemoRequestResource extends Resource
{
    protected static ?string $model = DemoRequest::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return \Filament\Support\Icons\Heroicon::OutlinedPresentationChartBar; }
    public static function getNavigationGroup(): ?string { return 'Inquiries'; }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'pending')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return DemoRequestForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return DemoRequestsTable::configure($table);
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
            'index' => ListDemoRequests::route('/'),
            'create' => CreateDemoRequest::route('/create'),
            'edit' => EditDemoRequest::route('/{record}/edit'),
        ];
    }
}
