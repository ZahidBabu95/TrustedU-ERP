<?php

namespace App\Filament\Resources\Testimonials\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class TestimonialsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')->circular()->defaultImageUrl('https://ui-avatars.com/api/?name=User'),
                TextColumn::make('name')->searchable(),
                TextColumn::make('institution')->searchable()->toggleable(),
                TextColumn::make('rating')->formatStateUsing(fn($state) => str_repeat('⭐', (int) $state)),
                IconColumn::make('is_featured')->boolean()->label('Featured'),
                IconColumn::make('is_active')->boolean(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->filters([
                TernaryFilter::make('is_active'),
                TernaryFilter::make('is_featured'),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}


