<?php

namespace App\Filament\Resources\ChatConversations;

use App\Filament\Resources\ChatConversations\Pages\ListChatConversations;
use App\Filament\Resources\ChatConversations\Pages\ViewChatConversation;
use App\Models\ChatConversation;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;

class ChatConversationResource extends Resource
{
    protected static ?string $model = ChatConversation::class;

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return \Filament\Support\Icons\Heroicon::OutlinedChatBubbleLeftRight;
    }

    public static function getNavigationGroup(): ?string { return 'Website CMS'; }
    protected static ?int $navigationSort = 6;
    protected static ?string $navigationLabel = 'Chat Support';
    protected static ?string $modelLabel = 'Chat Conversation';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count() ?: null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'success';
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('#')
                    ->sortable(),

                Tables\Columns\TextColumn::make('visitor_name')
                    ->label('Visitor')
                    ->default('Anonymous')
                    ->searchable()
                    ->icon('heroicon-o-user'),

                Tables\Columns\TextColumn::make('visitor_email')
                    ->label('Email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'active' => 'success',
                        'escalated' => 'warning',
                        'closed' => 'gray',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('message_count')
                    ->label('Messages')
                    ->sortable()
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('last_message_at')
                    ->label('Last Activity')
                    ->since()
                    ->sortable(),

                Tables\Columns\TextColumn::make('ip_address')
                    ->label('IP')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Started')
                    ->dateTime('d M Y, h:i A')
                    ->sortable(),
            ])
            ->defaultSort('last_message_at', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'active' => 'Active',
                        'escalated' => 'Escalated',
                        'closed' => 'Closed',
                    ]),
            ])
            ->actions([
                ViewAction::make(),
                Action::make('close')
                    ->label('Close')
                    ->icon('heroicon-o-x-circle')
                    ->color('gray')
                    ->requiresConfirmation()
                    ->action(fn (ChatConversation $record) => $record->update(['status' => 'closed']))
                    ->visible(fn (ChatConversation $record) => $record->status !== 'closed'),
            ]);
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListChatConversations::route('/'),
            'view' => ViewChatConversation::route('/{record}'),
        ];
    }
}
