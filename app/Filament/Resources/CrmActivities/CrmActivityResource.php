<?php

namespace App\Filament\Resources\CrmActivities;

use App\Filament\Resources\CrmActivities\Pages\ListCrmActivities;
use App\Models\CrmActivity;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CrmActivityResource extends Resource
{
    protected static ?string $model = CrmActivity::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-clock'; }
    public static function getNavigationGroup(): ?string { return 'CRM'; }
    public static function getNavigationLabel(): string { return 'Activity Log'; }
    protected static ?int $navigationSort = 11;

    public static function canCreate(): bool { return false; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->sortable()
                    ->tooltip(fn ($record) => $record->created_at->format('d M Y, h:i A')),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmActivity::TYPE_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'note'          => 'gray',
                        'call'          => 'info',
                        'meeting'       => 'success',
                        'email'         => 'primary',
                        'sms'           => 'warning',
                        'stage_change'  => 'warning',
                        'file_upload'   => 'gray',
                        'status_change' => 'info',
                        'task_complete' => 'success',
                        'follow_up'     => 'info',
                        'conversion'    => 'success',
                        'system'        => 'gray',
                        default         => 'gray',
                    }),
                TextColumn::make('entity_type')
                    ->label('Entity')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'lead'      => 'info',
                        'deal'      => 'warning',
                        'client'    => 'success',
                        'migration' => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => ucfirst($state)),
                TextColumn::make('entity_id')
                    ->label('ID')
                    ->sortable(),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->lineClamp(2),
                TextColumn::make('description')
                    ->searchable()
                    ->wrap()
                    ->lineClamp(2)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('user.name')
                    ->label('By')
                    ->sortable()
                    ->placeholder('System'),
            ])
            ->filters([
                SelectFilter::make('entity_type')
                    ->label('Entity Type')
                    ->options([
                        'lead'      => 'Lead',
                        'deal'      => 'Deal',
                        'client'    => 'Client',
                        'migration' => 'Migration',
                        'ticket'    => 'Ticket',
                    ])
                    ->multiple(),
                SelectFilter::make('type')
                    ->label('Activity Type')
                    ->options(CrmActivity::TYPE_LABELS)
                    ->multiple(),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmActivities::route('/'),
        ];
    }
}
