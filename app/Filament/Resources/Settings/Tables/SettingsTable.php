<?php

namespace App\Filament\Resources\Settings\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;

class SettingsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('label')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('key')
                    ->fontFamily('mono')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('value')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('group')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'general' => 'info',
                        'contact' => 'success',
                        'stats' => 'warning',
                        'links' => 'gray',
                        default => 'slate',
                    })
                    ->searchable(),
            ])
            ->filters([
                SelectFilter::make('group')
                    ->options([
                        'general' => 'General',
                        'contact' => 'Contact',
                        'stats' => 'Stats',
                        'links' => 'Links',
                    ])
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
