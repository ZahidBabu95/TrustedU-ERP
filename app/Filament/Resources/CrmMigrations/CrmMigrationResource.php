<?php

namespace App\Filament\Resources\CrmMigrations;

use App\Filament\Resources\CrmMigrations\Pages\EditCrmMigration;
use App\Filament\Resources\CrmMigrations\Pages\ListCrmMigrations;
use App\Filament\Resources\CrmMigrations\Pages\ViewCrmMigration;
use App\Models\CrmActivity;
use App\Models\CrmMigration;
use App\Models\CrmMigrationTask;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CrmMigrationResource extends Resource
{
    protected static ?string $model = CrmMigration::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-arrow-path'; }
    public static function getNavigationGroup(): ?string { return 'CRM'; }
    public static function getNavigationLabel(): string { return 'Migrations'; }
    protected static ?int $navigationSort = 3;

    public static function getNavigationBadge(): ?string
    {
        $count = CrmMigration::active()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return CrmMigration::active()->count() > 0 ? 'warning' : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Migration')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([

                    // ━━ TAB 1: Overview ━━
                    Tab::make('Overview')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Section::make('Migration Details')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('client.name')
                                        ->label('Client')
                                        ->disabled()
                                        ->prefixIcon('heroicon-o-building-office-2'),
                                    Select::make('status')
                                        ->options(CrmMigration::STATUS_LABELS)
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-signal'),
                                    Select::make('current_step')
                                        ->label('Current Step')
                                        ->options(CrmMigration::STEP_LABELS)
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-list-bullet'),
                                    Select::make('assigned_to')
                                        ->label('Assigned To')
                                        ->options(User::where('is_active', true)->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-user-circle'),
                                    TextInput::make('progress_percent')
                                        ->label('Progress')
                                        ->suffix('%')
                                        ->disabled()
                                        ->prefixIcon('heroicon-o-chart-bar'),
                                    TextInput::make('previous_software_name')
                                        ->label('Previous Software')
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-computer-desktop'),
                                ]),
                        ]),

                    // ━━ TAB 2: Schedule ━━
                    Tab::make('Schedule')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Section::make('Migration Timeline')
                                ->columns(2)
                                ->schema([
                                    DatePicker::make('migration_start_date')
                                        ->label('Start Date')
                                        ->native(false),
                                    DatePicker::make('migration_end_date')
                                        ->label('Expected End Date')
                                        ->native(false),
                                    DatePicker::make('actual_end_date')
                                        ->label('Actual End Date')
                                        ->native(false),
                                    TextInput::make('buffer_days')
                                        ->label('Buffer Days')
                                        ->numeric()
                                        ->default(5),
                                ]),

                            Section::make('Old System')
                                ->columns(2)
                                ->schema([
                                    Select::make('old_system_status')
                                        ->label('Old System Status')
                                        ->options(CrmMigration::OLD_SYSTEM_STATUS_LABELS)
                                        ->native(false),
                                    DatePicker::make('decommission_date')
                                        ->label('Decommission Date')
                                        ->native(false),
                                ]),
                        ]),

                    // ━━ TAB 3: Notes ━━
                    Tab::make('Notes')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Notes & Sign-off')
                                ->columns(2)
                                ->schema([
                                    Textarea::make('notes')
                                        ->label('Migration Notes')
                                        ->rows(4)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.client_id')
                    ->label('Client ID')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('xs')
                    ->copyable()
                    ->color('primary'),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->client?->institution_type ?? ''),
                TextColumn::make('current_step')
                    ->label('Current Step')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => CrmMigration::STEP_LABELS[$state] ?? $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'onboarding_plan'      => 'gray',
                        'data_processing'      => 'info',
                        'system_entry'         => 'primary',
                        'onboarding_checklist' => 'warning',
                        'training'             => 'info',
                        'handover'             => 'warning',
                        'invoice_deed'         => 'primary',
                        'completed'            => 'success',
                        default                => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmMigration::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'not_started'  => 'gray',
                        'in_progress'  => 'info',
                        'completed'    => 'success',
                        'failed'       => 'danger',
                        default        => 'gray',
                    }),
                TextColumn::make('progress_percent')
                    ->label('Progress')
                    ->suffix('%')
                    ->color(fn (?int $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state >= 50  => 'warning',
                        default       => 'gray',
                    })
                    ->alignCenter(),
                TextColumn::make('tasks_count')
                    ->label('Tasks')
                    ->counts('tasks')
                    ->sortable()
                    ->alignCenter(),
                TextColumn::make('assignee.name')
                    ->label('Assigned')
                    ->placeholder('—'),
                TextColumn::make('migration_start_date')
                    ->label('Start')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('migration_end_date')
                    ->label('Expected End')
                    ->date()
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->since()
                    ->label('Created')
                    ->size('xs')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CrmMigration::STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('current_step')
                    ->label('Step')
                    ->options(CrmMigration::STEP_LABELS)
                    ->multiple(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // ★ Advance to Next Step
                Action::make('advanceStep')
                    ->label(function (CrmMigration $r) {
                        $steps = CrmMigration::PIPELINE_STEPS;
                        $currentIdx = array_search($r->current_step, $steps);
                        if ($currentIdx === false || $currentIdx >= count($steps) - 1) return '—';
                        $next = $steps[$currentIdx + 1];
                        return '→ ' . (CrmMigration::STEP_LABELS[$next] ?? $next);
                    })
                    ->icon('heroicon-o-arrow-right')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Advance Migration Step?')
                    ->modalDescription(fn (CrmMigration $r) => 'Move from "' . (CrmMigration::STEP_LABELS[$r->current_step] ?? $r->current_step) . '" to next step?')
                    ->action(function (CrmMigration $record) {
                        $newStep = $record->advanceStep();
                        if (!$newStep) {
                            Notification::make()->title('Cannot advance further.')->warning()->send();
                            return;
                        }

                        CrmActivity::log('migration', $record->id, 'stage_change',
                            'Migration Step: ' . (CrmMigration::STEP_LABELS[$newStep] ?? $newStep),
                            null,
                            ['step' => $newStep, 'progress' => $record->progress_percent]
                        );

                        // Update client if completed
                        if ($newStep === 'completed' && $record->client) {
                            $record->client->update([
                                'pipeline_stage'        => 'active',
                                'implementation_status' => 'completed',
                                'is_live'               => true,
                                'activation_status'     => 'active',
                            ]);

                            CrmActivity::log('client', $record->client_id, 'conversion',
                                '🏆 Migration Completed! Client is now Active.',
                                null
                            );
                        }

                        Notification::make()
                            ->title('✅ Step Advanced')
                            ->body(CrmMigration::STEP_LABELS[$newStep] ?? $newStep)
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmMigration $r) => $r->current_step !== 'completed' && $r->status !== 'failed'),

                // Complete & Sign-off (only at invoice_deed step)
                Action::make('signoff')
                    ->label('🏆 Complete Migration')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Complete Migration & Sign-off')
                    ->modalDescription('This will mark the migration as completed. The client will become a paid active client.')
                    ->action(function (CrmMigration $record) {
                        $record->update([
                            'current_step'      => 'completed',
                            'status'            => 'completed',
                            'signoff_by'        => auth()->id(),
                            'signoff_at'        => now(),
                            'actual_end_date'   => now(),
                            'progress_percent'  => 100,
                            'old_system_status' => 'decommissioned',
                        ]);

                        if ($record->client) {
                            $record->client->update([
                                'pipeline_stage'        => 'active',
                                'implementation_status' => 'completed',
                                'is_live'               => true,
                                'activation_status'     => 'active',
                                'activation_date'       => now(),
                            ]);
                        }

                        CrmActivity::log('migration', $record->id, 'conversion',
                            '✅ Migration COMPLETED & Signed Off',
                            'Signed by: ' . auth()->user()->name
                        );
                        CrmActivity::log('client', $record->client_id, 'stage_change',
                            '🏆 Client is now ACTIVE (Paid Client)',
                            'Migration completed. Full system access granted.'
                        );

                        Notification::make()
                            ->title('🏆 Migration Completed!')
                            ->body('Client is now an active paid client.')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmMigration $r) =>
                        $r->current_step === 'invoice_deed' && $r->status !== 'completed'
                    ),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmMigrations::route('/'),
            'view'  => ViewCrmMigration::route('/{record}'),
            'edit'  => EditCrmMigration::route('/{record}/edit'),
        ];
    }
}
