<?php

namespace App\Filament\Resources\ErpModules\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class ErpModuleForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Module Info')->schema([
                TextInput::make('name')->required()->maxLength(150)->live(onBlur: true)
                    ->afterStateUpdated(fn($state, Set $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->required()->maxLength(150),
                TextInput::make('icon')->placeholder('heroicon-o-academic-cap')->maxLength(100),
                Select::make('color')
                    ->options([
                        'blue' => 'Blue', 'green' => 'Green', 'purple' => 'Purple',
                        'orange' => 'Orange', 'red' => 'Red', 'teal' => 'Teal',
                        'yellow' => 'Yellow', 'pink' => 'Pink',
                    ])->default('blue'),
                Textarea::make('description')->rows(3)->columnSpanFull(),
                TagsInput::make('features')
                    ->placeholder('Add a feature and press Enter')
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('Display')->schema([
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }
}
