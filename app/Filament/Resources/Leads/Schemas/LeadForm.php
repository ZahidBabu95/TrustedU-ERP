<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class LeadForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Lead Information')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255),
                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(30),
                    Select::make('source')
                        ->options([
                            'web'       => '🌐 Website',
                            'referral'  => '🤝 Referral',
                            'social'    => '📱 Social Media',
                            'cold_call' => '📞 Cold Call',
                            'email'     => '📧 Email',
                            'other'     => '📌 Other',
                        ])
                        ->default('web'),
                ]),

            Section::make('Status & Assignment')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options([
                            'new'       => 'New',
                            'contacted' => 'Contacted',
                            'qualified' => 'Qualified',
                            'proposal'  => 'Proposal Sent',
                            'won'       => '✅ Won',
                            'lost'      => '❌ Lost',
                        ])
                        ->default('new'),
                    Select::make('assigned_to')
                        ->label('Assign To')
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->preload(),
                    TextInput::make('value')
                        ->numeric()
                        ->prefix('৳')
                        ->label('Lead Value'),
                    DatePicker::make('expected_close_date')
                        ->label('Expected Close Date'),
                ]),

            Section::make('Notes')
                ->schema([
                    Textarea::make('notes')
                        ->rows(4)
                        ->columnSpanFull(),
                ]),
        ]);
    }
}
