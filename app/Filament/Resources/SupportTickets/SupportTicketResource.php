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
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
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
        return $schema->columns(1)->components([
            Grid::make(['lg' => 12])->schema([

                // ━━ LEFT COLUMN: Main Content (7/12) ━━
                Grid::make(['default' => 1])->schema([

                    // Section: Ticket Information
                    Section::make('Ticket Information')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            TextInput::make('ticket_number')
                                ->label('Ticket #')
                                ->default(fn () => 'TKT-' . strtoupper(Str::random(6)))
                                ->readonly()
                                ->required(),
                            TextInput::make('subject')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('Brief summary of the issue')
                                ->prefixIcon('heroicon-o-ticket'),
                            Grid::make(['md' => 2])->schema([
                                Select::make('category')
                                    ->label('Category')
                                    ->options(SupportTicket::CATEGORY_LABELS)
                                    ->native(false)
                                    ->placeholder('Select category')
                                    ->prefixIcon('heroicon-o-tag'),
                                Select::make('priority')
                                    ->options(SupportTicket::PRIORITY_LABELS)
                                    ->default('medium')
                                    ->required()
                                    ->native(false)
                                    ->prefixIcon('heroicon-o-flag'),
                            ]),
                        ]),

                    // Section: Issue Details
                    Section::make('Issue Details')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Textarea::make('description')
                                ->required()
                                ->rows(6)
                                ->placeholder('Please describe the issue in detail...')
                                ->columnSpanFull(),
                        ]),

                ])->columnSpan(['lg' => 7]),

                // ━━ RIGHT COLUMN: Sidebar (5/12) ━━
                Grid::make(['default' => 1])->schema([

                    // Section: Requester
                    Section::make('Requester Info')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Select::make('client_id')
                                ->label('Link Client')
                                ->relationship('client', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Search by name')
                                ->prefixIcon('heroicon-o-building-office'),
                            Select::make('user_id')
                                ->label('Reporting User')
                                ->relationship('user', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('User who reported')
                                ->prefixIcon('heroicon-o-user'),
                            TextInput::make('email')
                                ->email()
                                ->placeholder('Contact email for this ticket')
                                ->prefixIcon('heroicon-o-envelope'),
                        ]),

                    // Section: Meta & Assignment
                    Section::make('Assignment & Labels')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            Select::make('assigned_to')
                                ->label('Assigned Agent')
                                ->options(User::pluck('name', 'id'))
                                ->searchable()
                                ->preload()
                                ->default(fn () => auth()->id())
                                ->prefixIcon('heroicon-o-user-circle'),
                            Select::make('status')
                                ->options(SupportTicket::STATUS_LABELS)
                                ->default('open')
                                ->native(false)
                                ->prefixIcon('heroicon-o-signal'),
                            TagsInput::make('labels')
                                ->label('Labels')
                                ->placeholder('Add labels...'),
                            Checkbox::make('notify_requester')
                                ->label('Notify requester via email')
                                ->default(true),
                        ]),

                ])->columnSpan(['lg' => 5]),

            ]),
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
                    ->formatStateUsing(fn (string $state) => SupportTicket::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'open'        => 'info',
                        'in_progress' => 'warning',
                        'resolved'    => 'success',
                        'closed'      => 'gray',
                        default       => 'gray',
                    }),
                TextColumn::make('category')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => $state ? (SupportTicket::CATEGORY_LABELS[$state] ?? ucfirst($state)) : '—')
                    ->color('gray')
                    ->toggleable(isToggledHiddenByDefault: true),
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
                    ->options(SupportTicket::STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('priority')
                    ->options(SupportTicket::PRIORITY_LABELS),
                SelectFilter::make('category')
                    ->options(SupportTicket::CATEGORY_LABELS),
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
