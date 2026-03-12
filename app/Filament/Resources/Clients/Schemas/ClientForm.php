<?php

namespace App\Filament\Resources\Clients\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Institute Information')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('website')
                        ->url()
                        ->maxLength(255),
                    Select::make('institution_type')
                        ->options([
                            'school' => 'School',
                            'college' => 'College',
                            'university' => 'University',
                            'madrasha' => 'Madrasha',
                            'other' => 'Other',
                        ])->native(false),
                    TextInput::make('district')
                        ->maxLength(255),
                ])->columns(2),

            Section::make('Apperance & Branding')
                ->schema([
                    FileUpload::make('logo')
                        ->image()
                        ->disk('public')
                        ->directory('clients/logos')
                        ->imageEditor()
                        ->imagePreviewHeight('120')
                        ->downloadable(),
                    TextInput::make('sort_order')
                        ->numeric()
                        ->default(0),
                    Toggle::make('is_active')
                        ->default(true),
                    Toggle::make('is_featured')
                        ->default(false),
                ])->columns(2),
        ]);
    }
}
