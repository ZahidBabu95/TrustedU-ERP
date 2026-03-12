<?php

namespace App\Filament\Resources\Settings\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;

use Filament\Forms\Components\RichEditor;

class SettingForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Setting Configuration')
                    ->schema([
                        TextInput::make('key')
                            ->required()
                            ->disabled(fn ($record) => $record !== null),
                        TextInput::make('label')
                            ->required(),
                        Select::make('type')
                            ->options([
                                'text' => 'Text Header/Line',
                                'textarea' => 'Text Body/Paragraph',
                                'number' => 'Number',
                                'richtext' => 'Rich Text Editor',
                            ])
                            ->live()
                            ->required(),
                        Select::make('group')
                            ->options([
                                'general' => 'General Information',
                                'contact' => 'Contact Details',
                                'stats' => 'Statistics',
                                'links' => 'External Links',
                                'legal' => 'Legal & Policies',
                            ])
                            ->required(),
                        Textarea::make('value')
                            ->rows(4)
                            ->visible(fn ($get) => $get('type') !== 'richtext')
                            ->columnSpanFull(),
                        RichEditor::make('value')
                            ->visible(fn ($get) => $get('type') === 'richtext')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }
}
