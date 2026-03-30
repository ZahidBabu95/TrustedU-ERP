<?php

namespace App\Filament\Resources\CrmTrainings;

use App\Filament\Resources\CrmTrainings\Pages\CreateCrmTraining;
use App\Filament\Resources\CrmTrainings\Pages\EditCrmTraining;
use App\Filament\Resources\CrmTrainings\Pages\ListCrmTrainings;
use App\Filament\Resources\CrmTrainings\Pages\ViewCrmTraining;
use App\Models\Client;
use App\Models\CrmActivity;
use App\Models\CrmMigration;
use App\Models\CrmTraining;
use App\Models\ErpModule;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CrmTrainingResource extends Resource
{
    protected static ?string $model = CrmTraining::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-academic-cap'; }
    public static function getNavigationGroup(): ?string { return 'CRM'; }
    public static function getNavigationLabel(): string { return 'Training'; }
    protected static ?int $navigationSort = 4;

    public static function getNavigationBadge(): ?string
    {
        $count = CrmTraining::active()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return CrmTraining::active()->count() > 0 ? 'info' : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Training')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([

                    // ━━ TAB 1: Training Info ━━
                    Tab::make('Training Info')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Section::make('Basic Information')
                                ->columns(2)
                                ->schema([
                                    Select::make('client_id')
                                        ->label('Client (প্রতিষ্ঠান)')
                                        ->options(Client::where('is_active', true)->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->prefixIcon('heroicon-o-building-office-2'),
                                    Select::make('training_category')
                                        ->label('Training Category')
                                        ->options(CrmTraining::CATEGORY_LABELS)
                                        ->default('migration')
                                        ->native(false)
                                        ->required()
                                        ->prefixIcon('heroicon-o-tag'),
                                    TextInput::make('title')
                                        ->label('Training Title')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull()
                                        ->prefixIcon('heroicon-o-document-text')
                                        ->placeholder('e.g. Student Registration Module Training'),
                                    Select::make('training_type')
                                        ->label('Training Mode')
                                        ->options(CrmTraining::TYPE_LABELS)
                                        ->default('onsite')
                                        ->native(false)
                                        ->required()
                                        ->live()
                                        ->prefixIcon('heroicon-o-video-camera'),
                                    Select::make('status')
                                        ->options(CrmTraining::STATUS_LABELS)
                                        ->default('scheduled')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-signal'),
                                    Select::make('trainer_id')
                                        ->label('Trainer (কে ট্রেনিং দিবে)')
                                        ->options(User::where('is_active', true)->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->prefixIcon('heroicon-o-user-circle'),
                                    Select::make('migration_id')
                                        ->label('Migration (Optional)')
                                        ->options(fn () => CrmMigration::with('client')->get()->mapWithKeys(fn ($m) => [$m->id => ($m->client?->name ?? '#') . ' — Migration #' . $m->id]))
                                        ->searchable()
                                        ->preload()
                                        ->helperText('যদি কোনো Migration-এর সাথে সম্পর্কিত হয়')
                                        ->prefixIcon('heroicon-o-arrow-path'),
                                ]),

                            Section::make('Online Meeting Details')
                                ->columns(2)
                                ->description('অনলাইন ট্রেনিং হলে মিটিং লিংক ও প্ল্যাটফর্ম দিন')
                                ->schema([
                                    Select::make('meeting_platform')
                                        ->label('Platform')
                                        ->options(CrmTraining::PLATFORM_LABELS)
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-computer-desktop'),
                                    TextInput::make('meeting_link')
                                        ->label('Meeting Link/URL')
                                        ->url()
                                        ->maxLength(500)
                                        ->placeholder('https://zoom.us/j/...')
                                        ->prefixIcon('heroicon-o-link'),
                                    TextInput::make('venue')
                                        ->label('Venue (সরাসরি হলে)')
                                        ->maxLength(255)
                                        ->columnSpanFull()
                                        ->placeholder('প্রতিষ্ঠানের ঠিকানা বা ট্রেনিং স্থান')
                                        ->prefixIcon('heroicon-o-map-pin'),
                                ]),
                        ]),

                    // ━━ TAB 2: Schedule & Sessions ━━
                    Tab::make('Schedule')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Section::make('Schedule & Sessions')
                                ->columns(2)
                                ->schema([
                                    DatePicker::make('start_date')
                                        ->label('Start Date')
                                        ->native(false)
                                        ->required(),
                                    DatePicker::make('end_date')
                                        ->label('End Date')
                                        ->native(false),
                                    TimePicker::make('session_time')
                                        ->label('Session Time')
                                        ->native(false),
                                    TextInput::make('session_duration_minutes')
                                        ->label('Duration (minutes)')
                                        ->numeric()
                                        ->default(60)
                                        ->suffix('min')
                                        ->prefixIcon('heroicon-o-clock'),
                                    TextInput::make('total_sessions')
                                        ->label('Total Sessions')
                                        ->numeric()
                                        ->default(1)
                                        ->minValue(1)
                                        ->required()
                                        ->prefixIcon('heroicon-o-queue-list'),
                                    TextInput::make('completed_sessions')
                                        ->label('Completed Sessions')
                                        ->numeric()
                                        ->default(0)
                                        ->disabled()
                                        ->prefixIcon('heroicon-o-check'),
                                ]),
                        ]),

                    // ━━ TAB 3: Modules & Topics ━━
                    Tab::make('Modules & Topics')
                        ->icon('heroicon-o-squares-2x2')
                        ->schema([
                            Section::make('ERP Modules to Train')
                                ->description('কোন মডিউলগুলোতে ট্রেনিং দেওয়া হবে')
                                ->schema([
                                    Select::make('modules')
                                        ->label('ERP Modules')
                                        ->options(ErpModule::active()->ordered()->pluck('name', 'slug'))
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->columnSpanFull(),
                                ]),
                            Section::make('Training Topics')
                                ->description('ট্রেনিং-এ যে বিষয়গুলো আলোচনা করা হবে')
                                ->schema([
                                    Repeater::make('topics')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('topic')
                                                ->label('Topic')
                                                ->required()
                                                ->placeholder('যেমন: Student Entry, Fee Setup'),
                                            TextInput::make('duration')
                                                ->label('Duration')
                                                ->placeholder('30 min'),
                                        ])
                                        ->columns(2)
                                        ->addActionLabel('+ Add Topic')
                                        ->collapsible()
                                        ->defaultItems(0)
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ━━ TAB 4: Attendees ━━
                    Tab::make('Attendees')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Section::make('Training Attendees')
                                ->description('কারা ট্রেনিং-এ অংশ নিবে')
                                ->schema([
                                    Repeater::make('attendees')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('name')
                                                ->label('Name')
                                                ->required(),
                                            Select::make('role')
                                                ->label('Role')
                                                ->options(CrmTraining::ATTENDEE_ROLE_LABELS)
                                                ->native(false),
                                            TextInput::make('phone')
                                                ->label('Phone')
                                                ->tel(),
                                            TextInput::make('email')
                                                ->label('Email')
                                                ->email(),
                                        ])
                                        ->columns(4)
                                        ->addActionLabel('+ Add Attendee')
                                        ->collapsible()
                                        ->defaultItems(0)
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ━━ TAB 5: Notes & Feedback ━━
                    Tab::make('Notes & Feedback')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Notes & Feedback')
                                ->schema([
                                    Textarea::make('notes')
                                        ->label('Training Notes / Preparation')
                                        ->rows(4)
                                        ->columnSpanFull()
                                        ->placeholder('ট্রেনিং সম্পর্কে যেকোনো নোট...'),
                                    Textarea::make('feedback')
                                        ->label('Client Feedback (ট্রেনিং পরে)')
                                        ->rows(4)
                                        ->columnSpanFull()
                                        ->placeholder('ক্লায়েন্টের মন্তব্য, সন্তুষ্টি...'),
                                    TextInput::make('rating')
                                        ->label('Rating (1-5)')
                                        ->numeric()
                                        ->minValue(1)
                                        ->maxValue(5)
                                        ->suffix('/ 5')
                                        ->helperText('ক্লায়েন্ট ট্রেনিং-এ কতটুকু সন্তুষ্ট')
                                        ->prefixIcon('heroicon-o-star'),
                                ]),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->client?->client_id ?? ''),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->lineClamp(2)
                    ->weight('medium'),
                TextColumn::make('training_category')
                    ->label('Category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => CrmTraining::CATEGORY_LABELS[$state] ?? $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'migration'    => 'warning',
                        'onboarding'   => 'info',
                        'module'       => 'primary',
                        'advanced'     => 'success',
                        'refresher'    => 'gray',
                        'troubleshoot' => 'danger',
                        default        => 'gray',
                    }),
                TextColumn::make('training_type')
                    ->label('Mode')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmTraining::TYPE_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'onsite'      => 'success',
                        'online_zoom' => 'info',
                        'online_meet' => 'primary',
                        'video_call'  => 'warning',
                        'phone'       => 'gray',
                        'hybrid'      => 'warning',
                        default       => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmTraining::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'scheduled'   => 'info',
                        'in_progress' => 'warning',
                        'completed'   => 'success',
                        'cancelled'   => 'gray',
                        'postponed'   => 'warning',
                        default       => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'scheduled'   => 'heroicon-o-clock',
                        'in_progress' => 'heroicon-o-play',
                        'completed'   => 'heroicon-o-check-circle',
                        'cancelled'   => 'heroicon-o-x-circle',
                        'postponed'   => 'heroicon-o-pause',
                        default       => 'heroicon-o-academic-cap',
                    }),
                TextColumn::make('completed_sessions')
                    ->label('Sessions')
                    ->formatStateUsing(fn ($record) => "{$record->completed_sessions}/{$record->total_sessions}")
                    ->color(fn ($record) => match (true) {
                        $record->completed_sessions >= $record->total_sessions => 'success',
                        $record->completed_sessions > 0 => 'warning',
                        default => 'gray',
                    })
                    ->description(fn ($record) => $record->progress_percent . '%')
                    ->alignCenter(),
                TextColumn::make('trainer.name')
                    ->label('Trainer')
                    ->placeholder('—')
                    ->icon('heroicon-o-user'),
                TextColumn::make('start_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable()
                    ->description(fn ($record) => $record->session_time ? \Carbon\Carbon::parse($record->session_time)->format('h:i A') : '')
                    ->placeholder('—'),
                TextColumn::make('rating')
                    ->label('⭐')
                    ->formatStateUsing(fn (?int $state) => $state ? str_repeat('⭐', $state) : '—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CrmTraining::STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('training_category')
                    ->label('Category')
                    ->options(CrmTraining::CATEGORY_LABELS)
                    ->multiple(),
                SelectFilter::make('training_type')
                    ->label('Mode')
                    ->options(CrmTraining::TYPE_LABELS),
                SelectFilter::make('trainer_id')
                    ->label('Trainer')
                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // ★ Complete Session
                Action::make('completeSession')
                    ->label(fn (CrmTraining $r) => '✅ Session ' . ($r->completed_sessions + 1))
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (CrmTraining $r) =>
                        "Complete Session " . ($r->completed_sessions + 1) . " of {$r->total_sessions}?"
                    )
                    ->modalDescription(fn (CrmTraining $r) =>
                        "Training: {$r->title}"
                    )
                    ->form([
                        Textarea::make('session_summary')
                            ->label('Session Summary')
                            ->rows(2)
                            ->placeholder('আজকের সেশনে কী আলোচনা হয়েছে'),
                        TextInput::make('duration_actual')
                            ->label('Actual Duration')
                            ->placeholder('e.g. 45 min'),
                    ])
                    ->action(function (CrmTraining $record, array $data) {
                        $record->completeSession([
                            'summary'  => $data['session_summary'] ?? '',
                            'duration' => $data['duration_actual'] ?? '',
                        ]);

                        $msg = "Session {$record->completed_sessions}/{$record->total_sessions} completed";
                        if ($record->completed_sessions >= $record->total_sessions) {
                            $msg .= " — 🎉 Training Complete!";

                            if ($record->client && $record->client->pipeline_stage === 'training') {
                                $record->client->update(['pipeline_stage' => 'billing_active']);
                                CrmActivity::log('client', $record->client_id, 'stage_change',
                                    'Client moved to Billing Active (Training complete)'
                                );
                            }
                        }

                        Notification::make()
                            ->title('Session Completed')
                            ->body($msg)
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmTraining $r) => in_array($r->status, ['scheduled', 'in_progress'])
                        && $r->completed_sessions < $r->total_sessions
                    ),

                // Start Training
                Action::make('start')
                    ->label('▶ Start')
                    ->icon('heroicon-o-play')
                    ->color('info')
                    ->action(function (CrmTraining $record) {
                        $record->update(['status' => 'in_progress']);

                        CrmActivity::log('client', $record->client_id, 'status_change',
                            "Training started: {$record->title}",
                            "Type: " . (CrmTraining::TYPE_LABELS[$record->training_type] ?? $record->training_type)
                        );

                        Notification::make()->title('▶ Training Started')->info()->send();
                    })
                    ->visible(fn (CrmTraining $r) => $r->status === 'scheduled'),

                // Join Meeting
                Action::make('join')
                    ->label('🔗 Join')
                    ->icon('heroicon-o-link')
                    ->color('primary')
                    ->url(fn (CrmTraining $r) => $r->meeting_link, shouldOpenInNewTab: true)
                    ->visible(fn (CrmTraining $r) => !empty($r->meeting_link) && in_array($r->status, ['scheduled', 'in_progress'])),

                // Cancel
                Action::make('cancel')
                    ->label('Cancel')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (CrmTraining $record) {
                        $record->update(['status' => 'cancelled']);
                        Notification::make()->title('Training Cancelled')->warning()->send();
                    })
                    ->visible(fn (CrmTraining $r) => in_array($r->status, ['scheduled', 'in_progress'])),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('+ New Training')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('start_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCrmTrainings::route('/'),
            'create' => CreateCrmTraining::route('/create'),
            'view'   => ViewCrmTraining::route('/{record}'),
            'edit'   => EditCrmTraining::route('/{record}/edit'),
        ];
    }
}
