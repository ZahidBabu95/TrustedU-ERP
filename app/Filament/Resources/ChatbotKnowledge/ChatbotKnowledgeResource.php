<?php

namespace App\Filament\Resources\ChatbotKnowledge;

use App\Filament\Resources\ChatbotKnowledge\Pages\ListChatbotKnowledge;
use App\Filament\Resources\ChatbotKnowledge\Pages\CreateChatbotKnowledge;
use App\Filament\Resources\ChatbotKnowledge\Pages\EditChatbotKnowledge;
use App\Models\ChatbotKnowledgeBase;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ChatbotKnowledgeResource extends Resource
{
    protected static ?string $model = ChatbotKnowledgeBase::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return \Filament\Support\Icons\Heroicon::OutlinedBookOpen;
    }

    public static function getNavigationGroup(): ?string { return 'Platform'; }
    protected static ?int $navigationSort = 4;
    protected static ?string $navigationLabel = 'Chatbot Engine';
    protected static ?string $modelLabel = 'Knowledge Entry';
    protected static ?string $pluralModelLabel = 'Knowledge Base';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Question & Answer')
                ->description('Add a Q&A entry. The chatbot will use these as context when responding to visitors.')
                ->icon('heroicon-o-light-bulb')
                ->schema([
                    Grid::make(['md' => 2])->schema([
                        Select::make('category')
                            ->label('Category')
                            ->options(ChatbotKnowledgeBase::CATEGORIES)
                            ->required()
                            ->searchable(),
                        Select::make('language')
                            ->label('Language')
                            ->options([
                                'bn'   => '🇧🇩 Bengali',
                                'en'   => '🇬🇧 English',
                                'both' => '🌐 Both',
                            ])
                            ->default('both')
                            ->required(),
                    ]),
                    TextInput::make('question')
                        ->label('Question / Topic')
                        ->required()
                        ->maxLength(500)
                        ->placeholder('e.g. কীভাবে ডেমো বুক করব?')
                        ->helperText('The question or topic this entry answers'),
                    Textarea::make('answer')
                        ->label('Answer')
                        ->required()
                        ->rows(6)
                        ->placeholder('Enter the detailed answer...')
                        ->helperText('This will be used as AI context and also as a direct response'),
                ]),

            Section::make('Matching & Priority')
                ->description('Configure how this entry gets matched to user queries')
                ->schema([
                    TagsInput::make('keywords')
                        ->label('Keywords')
                        ->placeholder('Add keywords...')
                        ->helperText('Keywords help match user messages to this entry. Add Bengali & English variations.')
                        ->splitKeys(['Tab', ',', ' ']),
                    Grid::make(['md' => 3])->schema([
                        TextInput::make('priority')
                            ->label('Priority')
                            ->numeric()
                            ->default(0)
                            ->helperText('Higher = more important (0-100)'),
                        TextInput::make('sort_order')
                            ->label('Sort Order')
                            ->numeric()
                            ->default(0),
                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive entries are not shown'),
                    ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('category')
                    ->label('Category')
                    ->formatStateUsing(fn (string $state) => ChatbotKnowledgeBase::CATEGORIES[$state] ?? $state)
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('question')
                    ->label('Question')
                    ->searchable()
                    ->limit(60)
                    ->wrap(),

                Tables\Columns\TextColumn::make('answer')
                    ->label('Answer')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('language')
                    ->label('Lang')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'bn' => 'success',
                        'en' => 'info',
                        'both' => 'warning',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('keywords')
                    ->label('Keywords')
                    ->formatStateUsing(fn ($state) => is_array($state) ? implode(', ', array_slice($state, 0, 3)) . (count($state) > 3 ? '...' : '') : '')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('priority')
                    ->label('Priority')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('usage_count')
                    ->label('Used')
                    ->sortable()
                    ->alignCenter()
                    ->color('success'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
            ])
            ->defaultSort('priority', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('category')
                    ->options(ChatbotKnowledgeBase::CATEGORIES),
                Tables\Filters\SelectFilter::make('language')
                    ->options([
                        'bn'   => 'Bengali',
                        'en'   => 'English',
                        'both' => 'Both',
                    ]),
                Tables\Filters\TernaryFilter::make('is_active')
                    ->label('Active'),
            ])
            ->actions([
                EditAction::make(),
                Action::make('toggle')
                    ->label(fn (ChatbotKnowledgeBase $record) => $record->is_active ? 'Deactivate' : 'Activate')
                    ->icon(fn (ChatbotKnowledgeBase $record) => $record->is_active ? 'heroicon-o-x-circle' : 'heroicon-o-check-circle')
                    ->color(fn (ChatbotKnowledgeBase $record) => $record->is_active ? 'gray' : 'success')
                    ->action(fn (ChatbotKnowledgeBase $record) => $record->update(['is_active' => !$record->is_active]))
                    ->requiresConfirmation(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListChatbotKnowledge::route('/'),
            'create' => CreateChatbotKnowledge::route('/create'),
            'edit'   => EditChatbotKnowledge::route('/{record}/edit'),
        ];
    }
}
