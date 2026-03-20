<?php

namespace App\Filament\Resources\ErpModules\Schemas;

use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
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
                        'indigo' => 'Indigo', 'cyan' => 'Cyan',
                    ])->default('blue'),
                Textarea::make('description')->rows(3)->columnSpanFull()
                    ->helperText('Short description shown on landing page module cards.'),
                RichEditor::make('long_description')
                    ->columnSpanFull()
                    ->helperText('Detailed description shown on the module detail page.')
                    ->toolbarButtons([
                        'bold', 'italic', 'underline', 'strike',
                        'h2', 'h3',
                        'bulletList', 'orderedList',
                        'link', 'blockquote',
                    ]),
                TagsInput::make('features')
                    ->placeholder('Add a feature and press Enter')
                    ->helperText('Sub-features / sub-menus of this module.')
                    ->columnSpanFull(),
            ])->columns(2),

            Section::make('📹 Tutorial Videos (YouTube)')
                ->description('Add YouTube tutorial video links for this module. These will be displayed on the module detail page as a video library.')
                ->schema([
                    Repeater::make('youtube_videos')
                        ->label('')
                        ->schema([
                            TextInput::make('title')
                                ->required()
                                ->placeholder('e.g. How to Register a Student')
                                ->maxLength(200),
                            TextInput::make('url')
                                ->required()
                                ->url()
                                ->placeholder('https://www.youtube.com/watch?v=...')
                                ->helperText('Paste a YouTube video URL'),
                            Textarea::make('description')
                                ->placeholder('Brief description of this tutorial')
                                ->rows(2),
                        ])
                        ->columns(1)
                        ->collapsible()
                        ->cloneable()
                        ->reorderableWithButtons()
                        ->itemLabel(fn(array $state): ?string => $state['title'] ?? 'New Video')
                        ->defaultItems(0)
                        ->addActionLabel('➕ Add Tutorial Video')
                        ->columnSpanFull(),
                ]),

            Section::make('Display')->schema([
                TextInput::make('sort_order')->numeric()->default(0),
                Toggle::make('is_active')->default(true),
            ])->columns(2),
        ]);
    }
}
