<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use App\Models\User;
use Filament\Actions;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Str;

class ListSupportTickets extends ListRecords
{
    protected static string $resource = SupportTicketResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Quick Create Modal
            Actions\CreateAction::make('quick_create')
                ->label('Quick Ticket')
                ->icon('heroicon-o-bolt')
                ->color('primary')
                ->model(SupportTicket::class)
                ->form([
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(255)
                        ->placeholder('Brief subject of the issue'),
                    Select::make('priority')
                        ->options([
                            'low'    => '🟢 Low',
                            'medium' => '🔵 Medium',
                            'high'   => '🟠 High',
                            'urgent' => '🔴 Urgent',
                        ])
                        ->default('medium')
                        ->required(),
                    Select::make('client_id')
                        ->label('Client')
                        ->relationship('client', 'name')
                        ->searchable()
                        ->preload(),
                    Select::make('assigned_to')
                        ->label('Assign To')
                        ->options(User::where('is_active', true)->pluck('name', 'id'))
                        ->searchable()
                        ->default(fn () => auth()->id()),
                    Textarea::make('description')
                        ->required()
                        ->rows(3)
                        ->placeholder('Describe the issue...'),
                ])
                ->mutateFormDataUsing(function (array $data): array {
                    $data['ticket_number'] = 'TKT-' . strtoupper(Str::random(6));
                    $data['status'] = 'open';
                    $data['user_id'] = auth()->id();
                    return $data;
                }),

            // Full Create Page
            Actions\CreateAction::make()
                ->label('Full Form')
                ->icon('heroicon-o-plus')
                ->color('gray')
                ->url(fn () => SupportTicketResource::getUrl('create')),
        ];
    }
}
