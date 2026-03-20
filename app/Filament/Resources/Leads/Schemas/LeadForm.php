<?php

namespace App\Filament\Resources\Leads\Schemas;

use App\Models\Lead;
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
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-user'),
                    TextInput::make('company')
                        ->maxLength(255)
                        ->placeholder('Company / Organization')
                        ->prefixIcon('heroicon-o-building-office'),
                    TextInput::make('email')
                        ->email()
                        ->maxLength(255)
                        ->prefixIcon('heroicon-o-envelope'),
                    TextInput::make('phone')
                        ->tel()
                        ->maxLength(30)
                        ->prefixIcon('heroicon-o-phone'),
                    Select::make('source')
                        ->options(Lead::SOURCE_LABELS)
                        ->default('web')
                        ->native(false)
                        ->prefixIcon('heroicon-o-signal'),
                    Select::make('priority')
                        ->options(Lead::PRIORITY_LABELS)
                        ->default('medium')
                        ->native(false)
                        ->prefixIcon('heroicon-o-flag'),
                ]),

            Section::make('Status & Assignment')
                ->columns(2)
                ->schema([
                    Select::make('status')
                        ->options(Lead::STATUS_LABELS)
                        ->default('new')
                        ->native(false),
                    Select::make('assigned_to')
                        ->label('Assign To')
                        ->options(User::pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->prefixIcon('heroicon-o-user-circle'),
                    TextInput::make('value')
                        ->numeric()
                        ->prefix('৳')
                        ->label('Lead Value'),
                    DatePicker::make('expected_close_date')
                        ->label('Expected Close Date')
                        ->native(false)
                        ->prefixIcon('heroicon-o-calendar'),
                    TextInput::make('label')
                        ->maxLength(100)
                        ->placeholder('e.g. Hot Lead, Review Needed')
                        ->prefixIcon('heroicon-o-tag'),
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
