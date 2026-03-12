<?php

namespace App\Filament\Resources\Testimonials\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class TestimonialForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Person')->schema([
                TextInput::make('name')->required()->maxLength(150),
                TextInput::make('designation')->maxLength(150),
                TextInput::make('institution')->maxLength(200),
                FileUpload::make('avatar')->image()->directory('testimonials'),
            ])->columns(2),

            Section::make('Review')->schema([
                Textarea::make('message')->required()->rows(4)->columnSpanFull(),
                Select::make('rating')
                    ->options([1 => '⭐', 2 => '⭐⭐', 3 => '⭐⭐⭐', 4 => '⭐⭐⭐⭐', 5 => '⭐⭐⭐⭐⭐'])
                    ->default(5),
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_featured')->default(false),
                Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }
}
