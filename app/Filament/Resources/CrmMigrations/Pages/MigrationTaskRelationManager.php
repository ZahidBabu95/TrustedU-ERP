<?php

namespace App\Filament\Resources\CrmMigrations\Pages;

use App\Models\CrmMigrationTask;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class MigrationTaskRelationManager extends RelationManager
{
    protected static string $relationship = 'tasks';
    protected static ?string $title = 'Migration Tasks';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('title')
                ->required()
                ->maxLength(500)
                ->columnSpanFull(),
            Select::make('task_category')
                ->label('Category')
                ->options(CrmMigrationTask::CATEGORY_LABELS)
                ->required()
                ->native(false),
            Select::make('priority')
                ->options(CrmMigrationTask::PRIORITY_LABELS)
                ->default('medium')
                ->native(false),
            Select::make('status')
                ->options(CrmMigrationTask::STATUS_LABELS)
                ->default('pending')
                ->native(false),
            Select::make('assigned_to')
                ->label('Assigned To')
                ->options(User::where('is_active', true)->pluck('name', 'id'))
                ->searchable()
                ->preload(),
            DatePicker::make('due_date')
                ->label('Due Date')
                ->native(false),
            Textarea::make('notes')
                ->rows(3)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('sort_order')
                    ->label('#')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('task_category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmMigrationTask::CATEGORY_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'data_collection'  => 'info',
                        'data_structuring' => 'primary',
                        'data_cleaning'    => 'warning',
                        'data_import'      => 'warning',
                        'verification'     => 'success',
                        'system_config'    => 'info',
                        'user_setup'       => 'primary',
                        'decommission'     => 'danger',
                        default            => 'gray',
                    }),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->lineClamp(2),
                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmMigrationTask::PRIORITY_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'critical' => 'danger',
                        'high'     => 'warning',
                        'medium'   => 'info',
                        'low'      => 'gray',
                        default    => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmMigrationTask::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'completed'   => 'success',
                        'in_progress' => 'info',
                        'blocked'     => 'danger',
                        'skipped'     => 'gray',
                        default       => 'warning',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'completed'   => 'heroicon-o-check-circle',
                        'in_progress' => 'heroicon-o-play',
                        'blocked'     => 'heroicon-o-no-symbol',
                        'skipped'     => 'heroicon-o-forward',
                        default       => 'heroicon-o-clock',
                    }),
                TextColumn::make('assignee.name')
                    ->label('Assigned')
                    ->placeholder('—'),
                TextColumn::make('due_date')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) =>
                        $record->due_date && $record->due_date->isPast() && $record->status !== 'completed'
                            ? 'danger' : null
                    )
                    ->placeholder('—'),
                TextColumn::make('completed_at')
                    ->label('Done')
                    ->since()
                    ->placeholder('—'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CrmMigrationTask::STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('task_category')
                    ->label('Category')
                    ->options(CrmMigrationTask::CATEGORY_LABELS),
                SelectFilter::make('priority')
                    ->options(CrmMigrationTask::PRIORITY_LABELS),
            ])
            ->recordActions([
                EditAction::make(),

                // ✅ Complete Task
                Action::make('complete')
                    ->label('✅ Done')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (CrmMigrationTask $record) {
                        $record->markCompleted();

                        Notification::make()
                            ->title('Task Completed!')
                            ->body("Progress: {$record->migration->progress_percent}%")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmMigrationTask $record) => $record->status !== 'completed'),

                // Start task
                Action::make('start')
                    ->label('▶ Start')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->action(function (CrmMigrationTask $record) {
                        $record->update(['status' => 'in_progress']);
                        Notification::make()->title('Task Started')->info()->send();
                    })
                    ->visible(fn (CrmMigrationTask $record) => $record->status === 'pending'),

                // Block task
                Action::make('block')
                    ->label('⛔ Block')
                    ->icon('heroicon-o-no-symbol')
                    ->color('danger')
                    ->form([
                        Textarea::make('notes')
                            ->label('Block Reason')
                            ->required()
                            ->rows(2),
                    ])
                    ->action(function (CrmMigrationTask $record, array $data) {
                        $record->update([
                            'status' => 'blocked',
                            'notes'  => ($record->notes ? $record->notes . "\n" : '') . "Blocked: " . $data['notes'],
                        ]);
                        Notification::make()->title('Task Blocked')->warning()->send();
                    })
                    ->visible(fn (CrmMigrationTask $record) => in_array($record->status, ['pending', 'in_progress'])),
            ])
            ->defaultSort('sort_order', 'asc');
    }
}
