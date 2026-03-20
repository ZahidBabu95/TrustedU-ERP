<?php

namespace App\Filament\Resources\Deals\Tables;

use App\Models\Deal;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->company),
                TextColumn::make('stage')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'discovery'    => 'gray',
                        'prospecting'  => 'info',
                        'proposal'     => 'warning',
                        'negotiation'  => 'primary',
                        'contract'     => 'primary',
                        'closed_won'   => 'success',
                        'closed_lost'  => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => Deal::STAGE_LABELS[$state] ?? $state),
                TextColumn::make('value')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'success',
                        'medium' => 'warning',
                        'high'   => 'danger',
                        'urgent' => 'danger',
                        default  => 'gray',
                    }),
                TextColumn::make('probability')
                    ->suffix('%')
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expected_close_date')
                    ->date()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('stage')
                    ->options(Deal::STAGE_LABELS),
                SelectFilter::make('priority')
                    ->options(Deal::PRIORITY_LABELS),
                SelectFilter::make('deal_source')
                    ->label('Source')
                    ->options(Deal::SOURCE_LABELS),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
