<?php

namespace App\Filament\Resources\Clients\Tables;

use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Tables\Table;

class ClientsTable
{
    public static function configure(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('index')
                ->label('SL')
                ->rowIndex()
                ->width('50px'),
            TextColumn::make('client_id')
                ->label('Client ID')
                ->badge()
                ->color('info')
                ->searchable()
                ->sortable()
                ->copyable()
                ->copyMessage('Client ID copied'),
            ImageColumn::make('logo')
                ->disk(fn ($record) => $record->logo_disk ?? 'public')
                ->circular()
                ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->name ?? 'C') . '&background=3b82f6&color=fff'),
            TextColumn::make('name')
                ->label('Client Name')
                ->searchable()
                ->sortable()
                ->wrap()
                ->weight('bold'),
            TextColumn::make('institution_type')
                ->label('Type')
                ->badge()
                ->color(fn (?string $state): string => match ($state) {
                    'school'            => 'success',
                    'college'           => 'info',
                    'school_and_college' => 'primary',
                    'university'        => 'warning',
                    'madrasha'          => 'warning',
                    'coaching'          => 'gray',
                    'corporate'         => 'danger',
                    'ngo'               => 'warning',
                    default             => 'gray',
                })
                ->formatStateUsing(fn (?string $state): string => match ($state) {
                    'school'            => 'School',
                    'college'           => 'College',
                    'school_and_college' => 'School & College',
                    'university'        => 'University',
                    'madrasha'          => 'Madrasha',
                    'coaching'          => 'Coaching',
                    'corporate'         => 'Corporate',
                    'ngo'               => 'NGO',
                    default             => ucfirst($state ?? '—'),
                }),
            TextColumn::make('district')
                ->label('Location')
                ->sortable()
                ->icon('heroicon-m-map-pin')
                ->toggleable(),
            TextColumn::make('teams.name')
                ->label('Teams')
                ->badge()
                ->color('primary')
                ->placeholder('No Team')
                ->sortable()
                ->toggleable(),
            TextColumn::make('erp_modules_count')
                ->label('Modules')
                ->counts('erpModules')
                ->badge()
                ->color('info')
                ->sortable()
                ->toggleable(),
            ToggleColumn::make('is_active')
                ->label('Active'),
            ToggleColumn::make('is_live')
                ->label('Live')
                ->onColor('success')
                ->offColor('danger'),
            IconColumn::make('is_featured')
                ->boolean()
                ->label('Featured')
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('sort_order')
                ->label('Order')
                ->numeric()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
            TextColumn::make('created_at')
                ->label('Added')
                ->since()
                ->sortable()
                ->toggleable(isToggledHiddenByDefault: true),
        ])
        ->defaultSort('created_at', 'desc')
        ->filters([
            SelectFilter::make('institution_type')
                ->label('Type')
                ->options([
                    'school'            => 'School',
                    'college'           => 'College',
                    'school_and_college' => 'School & College',
                    'university'        => 'University',
                    'madrasha'          => 'Madrasha',
                    'coaching'          => 'Coaching Center',
                    'corporate'         => 'Corporate',
                    'ngo'               => 'NGO',
                    'other'             => 'Other',
                ]),
            SelectFilter::make('teams')
                ->label('Team')
                ->relationship('teams', 'name')
                ->searchable()
                ->preload(),
            TernaryFilter::make('is_live')
                ->label('Website Live')
                ->placeholder('All')
                ->trueLabel('Live')
                ->falseLabel('Not Live'),
            TernaryFilter::make('is_active')
                ->label('Active Status')
                ->placeholder('All')
                ->trueLabel('Active')
                ->falseLabel('Inactive'),
        ])
        ->recordActions([
            ViewAction::make(),
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
