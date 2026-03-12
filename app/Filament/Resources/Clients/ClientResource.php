<?php

namespace App\Filament\Resources\Clients;

use App\Filament\Resources\Clients\Pages;
use App\Models\Client;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Schemas\Schema;
use App\Filament\Resources\Clients\Schemas\ClientForm;
use App\Filament\Resources\Clients\Tables\ClientsTable;

class ClientResource extends Resource
{
    protected static ?string $model = Client::class;

    public static function getNavigationIcon(): string|\BackedEnum|null { return 'heroicon-o-building-office-2'; }
    
    public static function getNavigationGroup(): ?string { return 'Site Content'; }

    protected static ?string $label = 'Institutes';
    
    protected static ?string $pluralLabel = 'Institutes';

    public static function form(Schema $schema): Schema
    {
        return ClientForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientsTable::configure($table);
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
            'index' => Pages\ListClients::route('/'),
            'create' => Pages\CreateClient::route('/create'),
            'edit' => Pages\EditClient::route('/{record}/edit'),
        ];
    }
}
