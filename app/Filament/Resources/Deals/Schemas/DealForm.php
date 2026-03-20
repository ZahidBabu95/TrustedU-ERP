<?php

namespace App\Filament\Resources\Deals\Schemas;

use App\Models\Client;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Deal')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([

                    // ━━ TAB 1: Deal Info ━━
                    Tab::make('Deal Info')
                        ->icon('heroicon-o-briefcase')
                        ->schema([
                            Section::make('Deal Details')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('title')
                                        ->required()
                                        ->maxLength(255)
                                        ->label('Deal Title')
                                        ->placeholder('e.g. ERP Implementation')
                                        ->prefixIcon('heroicon-o-briefcase'),
                                    TextInput::make('company')
                                        ->maxLength(255)
                                        ->placeholder('Company / Organization')
                                        ->prefixIcon('heroicon-o-building-office'),
                                    TextInput::make('value')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->label('Deal Value'),
                                    Select::make('deal_source')
                                        ->label('Source')
                                        ->options(Deal::SOURCE_LABELS)
                                        ->default('direct')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-signal'),
                                    Select::make('priority')
                                        ->options(Deal::PRIORITY_LABELS)
                                        ->default('medium')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-flag'),
                                    TextInput::make('label')
                                        ->maxLength(100)
                                        ->placeholder('e.g. Hot Deal, Renewal')
                                        ->prefixIcon('heroicon-o-tag'),
                                ]),
                        ]),

                    // ━━ TAB 2: Contact ━━
                    Tab::make('Contact')
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Section::make('Contact Person')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('contact_name')
                                        ->label('Contact Name')
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-user'),
                                    TextInput::make('contact_email')
                                        ->label('Email')
                                        ->email()
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-envelope'),
                                    TextInput::make('contact_phone')
                                        ->label('Phone')
                                        ->tel()
                                        ->maxLength(30)
                                        ->prefixIcon('heroicon-o-phone'),
                                ]),
                        ]),

                    // ━━ TAB 3: Pipeline & Assignment ━━
                    Tab::make('Pipeline')
                        ->icon('heroicon-o-funnel')
                        ->schema([
                            Section::make('Pipeline Stage')
                                ->columns(2)
                                ->schema([
                                    Select::make('stage')
                                        ->label('Stage')
                                        ->options(Deal::STAGE_LABELS)
                                        ->default('discovery')
                                        ->native(false),
                                    TextInput::make('probability')
                                        ->label('Win Probability (%)')
                                        ->numeric()
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->suffix('%')
                                        ->prefixIcon('heroicon-o-chart-bar'),
                                    Select::make('assigned_to')
                                        ->label('Assign To')
                                        ->options(User::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-user-circle'),
                                    Select::make('lead_id')
                                        ->label('Linked Lead')
                                        ->options(Lead::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-link'),
                                    Select::make('client_id')
                                        ->label('Linked Client')
                                        ->options(Client::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-building-office-2'),
                                    DatePicker::make('expected_close_date')
                                        ->label('Expected Close Date')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    DatePicker::make('closed_at')
                                        ->label('Closed Date')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-calendar-days'),
                                ]),
                        ]),

                    // ━━ TAB 4: Notes ━━
                    Tab::make('Notes')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Additional Notes')
                                ->schema([
                                    Textarea::make('notes')
                                        ->rows(5)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ]);
    }
}
