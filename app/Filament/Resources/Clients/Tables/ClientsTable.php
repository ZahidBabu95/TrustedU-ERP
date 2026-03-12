<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            ImageColumn::make('logo')
                ->disk('public')
                ->circular()
                ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?name=School&background=3b82f6&color=fff'),
            TextColumn::make('name')
                ->searchable()
                ->sortable()
                ->wrap(),
            TextColumn::make('institution_type')
                ->badge()
                ->color('gray'),
            TextColumn::make('district')
                ->sortable(),
            ToggleColumn::make('is_active')
                ->label('Active'),
            IconColumn::make('is_featured')
                ->boolean()
                ->label('Featured'),
            TextColumn::make('sort_order')
                ->numeric()
                ->sortable(),
        ])
        ->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])
        ->toolbarActions([
            BulkActionGroup::make([
                DeleteBulkAction::make(),
            ]),
        ]);
    }
}
