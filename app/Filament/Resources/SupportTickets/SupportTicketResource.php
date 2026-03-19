<?php

namespace App\Filament\Resources\SupportTickets;

use App\Filament\Resources\SupportTickets\Pages\CreateSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\EditSupportTicket;
use App\Filament\Resources\SupportTickets\Pages\ListSupportTickets;
use App\Filament\Resources\SupportTickets\Pages\ViewSupportTicket;
use App\Models\SupportTicket;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class SupportTicketResource extends Resource
{
    protected static ?string $model = SupportTicket::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-lifebuoy'; }
    public static function getNavigationGroup(): ?string { return 'Management'; }
    protected static ?int $navigationSort = 2;
    protected static ?string $navigationLabel = 'Support';
    protected static ?string $modelLabel = 'Ticket';
    protected static ?string $pluralModelLabel = 'Support Tickets';

    protected static ?string $recordTitleAttribute = 'subject';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', 'open')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Ticket')
                ->tabs([
                    Tab::make('Basic Info')
                        ->icon('heroicon-o-ticket')
                        ->schema([
                            Section::make('Ticket Details')
                                ->description('Create a new support ticket')
                                ->schema([
                                    Grid::make(['md' => 2])->schema([
                                        TextInput::make('ticket_number')
                                            ->label('Ticket #')
                                            ->default(fn () => 'TKT-' . strtoupper(Str::random(6)))
                                            ->readonly()
                                            ->required(),
                                        TextInput::make('subject')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Describe the issue briefly')
                                            ->columnSpanFull(),
                                    ]),
                                    Grid::make(['md' => 2])->schema([
                                        Select::make('priority')
                                            ->options([
                                                'low'    => '🟢 Low',
                                                'medium' => '🔵 Medium',
                                                'high'   => '🟠 High',
                                                'urgent' => '🔴 Urgent',
                                            ])
                                            ->default('medium')
                                            ->required(),
                                        Select::make('status')
                                            ->options([
                                                'open'        => '🔵 Open',
                                                'in_progress' => '🟡 In Progress',
                                                'resolved'    => '🟢 Resolved',
                                                'closed'      => '⚪ Closed',
                                            ])
                                            ->default('open'),
                                    ]),
                                    Textarea::make('description')
                                        ->required()
                                        ->rows(4)
                                        ->placeholder('Describe the issue in detail...')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    Tab::make('Assignment')
                        ->icon('heroicon-o-user-circle')
                        ->schema([
                            Section::make('People')
                                ->description('Assign client and agent')
                                ->schema([
                                    Grid::make(['md' => 2])->schema([
                                        Select::make('client_id')
                                            ->label('Client / Institute')
                                            ->relationship('client', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('Select client'),
                                        Select::make('user_id')
                                            ->label('Reporting User')
                                            ->relationship('user', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->placeholder('User who reported'),
                                    ]),
                                    Grid::make(['md' => 2])->schema([
                                        Select::make('assigned_to')
                                            ->label('Assigned Agent')
                                            ->options(User::where('is_active', true)->pluck('name', 'id'))
                                            ->searchable()
                                            ->preload()
                                            ->default(fn () => auth()->id())
                                            ->placeholder('Assign to agent'),
                                        Select::make('category')
                                            ->label('Category')
                                            ->options([
                                                'technical'  => 'Technical Issue',
                                                'billing'    => 'Billing',
                                                'feature'    => 'Feature Request',
                                                'bug'        => 'Bug Report',
                                                'general'    => 'General Inquiry',
                                                'onboarding' => 'Onboarding Help',
                                            ])
                                            ->placeholder('Select category'),
                                    ]),
                                    TextInput::make('email')
                                        ->email()
                                        ->placeholder('Contact email for this ticket'),
                                ]),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('ticket_number')
                    ->label('Ticket #')
                    ->searchable()
                    ->weight('bold')
                    ->copyable()
                    ->color('primary')
                    ->size('sm'),
                TextColumn::make('subject')
                    ->searchable()
                    ->limit(45)
                    ->tooltip(fn ($record) => $record->subject)
                    ->weight('medium'),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->default('—')
                    ->icon('heroicon-o-building-office')
                    ->size('sm'),
                TextColumn::make('priority')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucfirst($state))
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'gray',
                        'medium' => 'info',
                        'high'   => 'warning',
                        'urgent' => 'danger',
                        default  => 'gray',
                    }),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => str_replace('_', ' ', ucwords($state, '_')))
                    ->color(fn (string $state): string => match ($state) {
                        'open'        => 'info',
                        'in_progress' => 'warning',
                        'resolved'    => 'success',
                        'closed'      => 'gray',
                        default       => 'gray',
                    }),
                TextColumn::make('assignee.name')
                    ->label('Agent')
                    ->default('Unassigned')
                    ->icon('heroicon-o-user')
                    ->color('gray')
                    ->size('sm'),
                TextColumn::make('messages_count')
                    ->label('Replies')
                    ->counts('messages')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('updated_at')
                    ->label('Last Updated')
                    ->since()
                    ->sortable()
                    ->color('gray')
                    ->size('sm'),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options([
                        'open'        => 'Open',
                        'in_progress' => 'In Progress',
                        'resolved'    => 'Resolved',
                        'closed'      => 'Closed',
                    ])
                    ->multiple(),
                SelectFilter::make('priority')
                    ->options([
                        'low'    => 'Low',
                        'medium' => 'Medium',
                        'high'   => 'High',
                        'urgent' => 'Urgent',
                    ]),
                SelectFilter::make('assigned_to')
                    ->label('Agent')
                    ->relationship('assignee', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->emptyStateHeading('No support tickets yet')
            ->emptyStateDescription('Create your first ticket to start managing support.')
            ->emptyStateIcon('heroicon-o-ticket')
            ->poll('30s')
            ->recordUrl(fn ($record) => static::getUrl('view', ['record' => $record]));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListSupportTickets::route('/'),
            'create' => CreateSupportTicket::route('/create'),
            'view'   => ViewSupportTicket::route('/{record}'),
            'edit'   => EditSupportTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class])
            ->teamScoped();
    }
}
