<?php

namespace App\Filament\Resources\ErpModules\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class ErpModulesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')->sortable()->label('#'),
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('slug')->searchable()->color('gray'),
                TextColumn::make('color')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([TernaryFilter::make('is_active')])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}


