<?php

namespace App\Filament\Resources\ContactMessages\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class ContactMessageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            TextInput::make('name')->required()->maxLength(150),
            TextInput::make('email')->email()->required(),
            TextInput::make('phone')->maxLength(30),
            TextInput::make('subject')->maxLength(255),
            Select::make('status')
                ->options(['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived'])
                ->default('new'),
            Textarea::make('message')->required()->rows(5)->columnSpanFull(),
        ])->columns(2);
    }
}
