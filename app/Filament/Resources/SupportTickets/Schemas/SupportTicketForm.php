<?php

namespace App\Filament\Resources\SupportTickets\Schemas;

use App\Models\User;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class SupportTicketForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Ticket Details')
                ->columns(2)
                ->schema([
                    TextInput::make('ticket_number')
                        ->label('Ticket #')
                        ->default(fn () => 'TKT-' . strtoupper(Str::random(6)))
                        ->readonly()
                        ->required(),
                    TextInput::make('subject')
                        ->required()
                        ->maxLength(255),
                    Select::make('priority')
                        ->options([
                            'low'    => '🟢 Low',
                            'medium' => '🟡 Medium',
                            'high'   => '🔴 High',
                            'urgent' => '🚨 Urgent',
                        ])
                        ->default('medium'),
                    Select::make('status')
                        ->options([
                            'open'        => 'Open',
                            'in_progress' => 'In Progress',
                            'resolved'    => 'Resolved',
                            'closed'      => 'Closed',
                        ])
                        ->default('open'),
                    Select::make('assigned_to')
                        ->label('Assign To')
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                    Select::make('client_id')
                        ->label('Client / Institute')
                        ->relationship('client', 'name')
                        ->searchable()
                        ->preload(),
                ]),

            Section::make('Description')
                ->schema([
                    Textarea::make('description')
                        ->required()
                        ->rows(5)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
