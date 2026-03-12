<?php

namespace App\Filament\Resources\HeroSections\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class HeroSectionForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Hero Content')->schema([
                TextInput::make('headline')->required()->maxLength(255)->columnSpanFull(),
                Textarea::make('subheadline')->rows(2)->columnSpanFull(),
                TextInput::make('cta_primary_text')->maxLength(100)->label('Primary Button Text'),
                TextInput::make('cta_primary_url')->url()->label('Primary Button URL'),
                TextInput::make('cta_secondary_text')->maxLength(100)->label('Secondary Button Text'),
                TextInput::make('cta_secondary_url')->url()->label('Secondary Button URL'),
            ])->columns(2),

            Section::make('Media & Display')->schema([
                FileUpload::make('background_image')->image()->directory('hero')->columnSpanFull(),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }
}
