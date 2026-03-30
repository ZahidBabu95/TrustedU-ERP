<?php

namespace App\Filament\Resources\CrmFollowUps;

use App\Filament\Resources\CrmFollowUps\Pages\CreateCrmFollowUp;
use App\Filament\Resources\CrmFollowUps\Pages\ListCrmFollowUps;
use App\Models\CrmFollowUp;
use App\Models\User;
use BackedEnum;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;

class CrmFollowUpResource extends Resource
{
    protected static ?string $model = CrmFollowUp::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-calendar-days'; }
    public static function getNavigationGroup(): ?string { return 'CRM'; }
    public static function getNavigationLabel(): string { return 'Follow-ups'; }
    protected static ?int $navigationSort = 10;

    public static function getNavigationBadge(): ?string
    {
        $count = CrmFollowUp::today()->pending()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $overdue = CrmFollowUp::overdue()->count();
        return $overdue > 0 ? 'danger' : 'primary';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Follow-up Details')
                ->columns(2)
                ->schema([
                    Select::make('entity_type')
                        ->label('Entity Type')
                        ->options([
                            'lead'   => 'Lead',
                            'deal'   => 'Deal',
                            'client' => 'Client',
                        ])
                        ->required()
                        ->native(false),
                    TextInput::make('entity_id')
                        ->label('Entity ID')
                        ->required()
                        ->numeric(),
                    Select::make('type')
                        ->label('Follow-up Type')
                        ->options(CrmFollowUp::TYPE_LABELS)
                        ->required()
                        ->native(false)
                        ->default('call'),
                    Select::make('priority')
                        ->options(CrmFollowUp::PRIORITY_LABELS)
                        ->default('medium')
                        ->native(false),
                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->columnSpanFull(),
                    DateTimePicker::make('scheduled_at')
                        ->label('Scheduled At')
                        ->required()
                        ->native(false),
                    Select::make('assigned_to')
                        ->label('Assigned To')
                        ->options(User::where('is_active', true)->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required()
                        ->default(fn () => auth()->id()),
                    Textarea::make('description')
                        ->rows(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('scheduled_at')
                    ->label('Scheduled')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->color(fn ($record) => $record->scheduled_at->isPast() && $record->status === 'pending' ? 'danger' : null),
                TextColumn::make('type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmFollowUp::TYPE_LABELS[$state] ?? $state),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->lineClamp(2),
                TextColumn::make('entity_type')
                    ->label('Entity')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state) => match ($state) {
                        'lead'   => 'info',
                        'deal'   => 'warning',
                        'client' => 'success',
                        default  => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmFollowUp::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state) => match ($state) {
                        'pending'     => 'warning',
                        'completed'   => 'success',
                        'missed'      => 'danger',
                        'cancelled'   => 'gray',
                        'rescheduled' => 'info',
                        default       => 'gray',
                    }),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state) => match ($state) {
                        'urgent' => 'danger',
                        'high'   => 'warning',
                        'medium' => 'info',
                        'low'    => 'gray',
                        default  => 'gray',
                    }),
                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->placeholder('—'),
                TextColumn::make('outcome')
                    ->wrap()
                    ->lineClamp(1)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CrmFollowUp::STATUS_LABELS)
                    ->multiple()
                    ->default(['pending']),
                SelectFilter::make('type')
                    ->options(CrmFollowUp::TYPE_LABELS),
                SelectFilter::make('entity_type')
                    ->label('Entity')
                    ->options([
                        'lead'   => 'Lead',
                        'deal'   => 'Deal',
                        'client' => 'Client',
                    ]),
                SelectFilter::make('priority')
                    ->options(CrmFollowUp::PRIORITY_LABELS),
            ])
            ->recordActions([
                EditAction::make(),

                // Complete follow-up
                Action::make('complete')
                    ->label('✅ Done')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->form([
                        Textarea::make('outcome')
                            ->label('Outcome / Notes')
                            ->rows(3),
                    ])
                    ->action(function (CrmFollowUp $record, array $data) {
                        $record->markCompleted($data['outcome'] ?? null);

                        Notification::make()
                            ->title('Follow-up completed!')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmFollowUp $record) => $record->status === 'pending'),

                // Mark as missed
                Action::make('missed')
                    ->label('❌ Missed')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (CrmFollowUp $record) {
                        $record->markMissed();

                        Notification::make()
                            ->title('Follow-up marked as missed')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (CrmFollowUp $record) => $record->status === 'pending'),
            ])
            ->defaultSort('scheduled_at', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCrmFollowUps::route('/'),
            'create' => CreateCrmFollowUp::route('/create'),
        ];
    }
}
