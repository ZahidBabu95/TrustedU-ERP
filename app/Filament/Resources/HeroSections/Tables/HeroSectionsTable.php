<?php

namespace App\Filament\Resources\HeroSections\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\EditAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class HeroSectionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('headline')->searchable()->limit(50)->weight('bold'),
                ImageColumn::make('background_image')->width(80),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('sort_order')->sortable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([TernaryFilter::make('is_active')])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}


