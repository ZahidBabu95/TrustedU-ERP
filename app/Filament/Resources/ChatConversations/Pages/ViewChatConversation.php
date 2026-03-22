<?php

namespace App\Filament\Resources\ChatConversations\Pages;

use App\Filament\Resources\ChatConversations\ChatConversationResource;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ViewChatConversation extends ViewRecord
{
    protected static string $resource = ChatConversationResource::class;

    public function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Conversation Info')
                ->schema([
                    TextEntry::make('visitor_name')
                        ->label('Visitor Name')
                        ->default('Anonymous'),
                    TextEntry::make('visitor_email')
                        ->label('Email'),
                    TextEntry::make('visitor_phone')
                        ->label('Phone'),
                    TextEntry::make('status')
                        ->badge()
                        ->color(fn (string $state): string => match ($state) {
                            'active' => 'success',
                            'escalated' => 'warning',
                            'closed' => 'gray',
                            default => 'gray',
                        }),
                    TextEntry::make('ip_address')
                        ->label('IP Address'),
                    TextEntry::make('created_at')
                        ->label('Started')
                        ->dateTime('d M Y, h:i A'),
                    TextEntry::make('message_count')
                        ->label('Total Messages'),
                ]),

            Section::make('Chat History')
                ->schema([
                    ViewEntry::make('messages')
                        ->view('filament.resources.chat-conversations.chat-history'),
                ]),
        ]);
    }
}
