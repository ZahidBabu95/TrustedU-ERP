<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->company),
                TextColumn::make('email')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new'          => 'gray',
                        'contacted'    => 'warning',
                        'qualified'    => 'primary',
                        'proposal'     => 'info',
                        'negotiation'  => 'primary',
                        'won'          => 'success',
                        'lost'         => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => Lead::STATUS_LABELS[$state] ?? $state),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'success',
                        'medium' => 'warning',
                        'high'   => 'danger',
                        'urgent' => 'danger',
                        default  => 'gray',
                    }),
                TextColumn::make('value')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable(),
                TextColumn::make('source')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Lead::SOURCE_LABELS[$state] ?? $state),
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
                SelectFilter::make('status')
                    ->options(Lead::STATUS_LABELS),
                SelectFilter::make('source')
                    ->options(Lead::SOURCE_LABELS),
                SelectFilter::make('priority')
                    ->options(Lead::PRIORITY_LABELS),
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
