<?php

namespace App\Filament\Resources\ErpModules\Schemas;

use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Builder;
use Filament\Forms\Components\Builder\Block;
use Filament\Forms\Components\ColorPicker;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class ErpModuleForm
{
    public static function getStorageDisk(): string
    {
        return 'public'; // Forced local storage to bypass CORS and simplify cPanel deployment
    }

    private static function getDesignSchema(): array
    {
        return [
            Section::make('🎨 Design & Layout Settings')
                ->schema([
                    ColorPicker::make('bg_color')->label('Custom Background Color'),
                    ColorPicker::make('text_color')->label('Custom Text Color'),
                    Select::make('padding_y')
                        ->label('Vertical Padding (Spacing)')
                        ->options([
                            'py-10' => 'Small',
                            'py-20' => 'Medium (Default)',
                            'py-32' => 'Large',
                            'py-0' => 'None',
                        ])->default('py-20'),
                    TextInput::make('custom_classes')->label('Advanced Tailwind Classes')->placeholder('e.g. shadow-lg rounded-2xl'),
                ])
                ->columns(2)
                ->collapsed(),
        ];
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Module Settings')
                ->tabs([
                    Tab::make('🟢 Basic Info')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            TextInput::make('name')
                                ->label('Module Name')
                                ->required()
                                ->maxLength(150)
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn($state, Set $set) => $set('slug', Str::slug($state))),
                            TextInput::make('slug')
                                ->label('URL Slug')
                                ->required()
                                ->maxLength(150),
                                
                            Select::make('color')
                                ->label('Theme Color')
                                ->options([
                                    'blue' => 'Blue', 'green' => 'Green', 'purple' => 'Purple',
                                    'orange' => 'Orange', 'red' => 'Red', 'teal' => 'Teal',
                                    'yellow' => 'Yellow', 'pink' => 'Pink', 'indigo' => 'Indigo', 'cyan' => 'Cyan',
                                ])->default('blue')->native(false),

                            FileUpload::make('icon_image')
                                ->label('Module Icon (Image/File)')
                                ->disk(self::getStorageDisk())
                                ->directory('modules/icons')
                                ->columnSpanFull(),

                            Textarea::make('description')
                                ->label('Short Description')
                                ->rows(3)
                                ->columnSpanFull(),
                        ])->columns(2),

                    Tab::make('💻 Legacy Info')
                        ->icon('heroicon-o-archive-box')
                        ->schema([
                            Section::make('Hero Section (Legacy)')->schema([
                                TextInput::make('hero_subtitle')->maxLength(300),
                                FileUpload::make('hero_image')->disk(self::getStorageDisk())->directory('modules/hero'),
                            ])->columns(2),
                            TagsInput::make('features')->label('Key Features (Legacy)')->columnSpanFull(),
                            Repeater::make('youtube_videos')
                                ->label('Tutorial Videos (Legacy)')
                                ->schema([
                                    TextInput::make('title')->required(),
                                    TextInput::make('url')->required()->url(),
                                ])->columns(2),
                        ]),

                    Tab::make('🚀 Dynamic Builder')
                        ->icon('heroicon-o-squares-plus')
                        ->schema([
                            Builder::make('dynamic_sections')
                                ->label('Add Sections to Landing Page')
                                ->blocks([
                                    Block::make('hero')
                                        ->label('Hero Block')
                                        ->icon('heroicon-o-stop')
                                        ->schema(array_merge([
                                            TextInput::make('title')->label('Hero Title')->required(),
                                            Textarea::make('subtitle')->label('Hero Subtitle')->rows(2),
                                            FileUpload::make('image')->label('Hero Image/Media')->disk(self::getStorageDisk())->directory('modules/builder'),
                                            Repeater::make('buttons')
                                                ->schema([
                                                    TextInput::make('label')->required(),
                                                    TextInput::make('url')->required(),
                                                    Select::make('style')->options(['primary' => 'Primary', 'outline' => 'Outline'])->default('primary'),
                                                    ColorPicker::make('button_color')->label('Custom Button Color (Optional)'),
                                                ])->columns(2)->maxItems(2),
                                        ], self::getDesignSchema())),
                                        
                                    Block::make('features_grid')
                                        ->label('Features Grid')
                                        ->icon('heroicon-o-squares-2x2')
                                        ->schema(array_merge([
                                            TextInput::make('section_title')->default('Key Features'),
                                            TextInput::make('section_subtitle'),
                                            Repeater::make('features')
                                                ->schema([
                                                    TextInput::make('title')->required(),
                                                    Textarea::make('description')->rows(2)->required(),
                                                    TextInput::make('icon')->label('Heroicon SVG name')->placeholder('e.g. heroicon-o-check'),
                                                ])->columns(1),
                                        ], self::getDesignSchema())),

                                    Block::make('rich_content')
                                        ->label('Rich Text & Image Section')
                                        ->icon('heroicon-o-document-text')
                                        ->schema(array_merge([
                                            TextInput::make('section_title')->label('Title'),
                                            RichEditor::make('content')->label('Main Content')->required(),
                                            FileUpload::make('image')->label('Media/File')->disk(self::getStorageDisk())->directory('modules/content'),
                                            Select::make('image_position')
                                                ->options(['left' => 'Image on Left', 'right' => 'Image on Right', 'top' => 'Image on Top'])
                                                ->default('right'),
                                        ], self::getDesignSchema())),

                                    Block::make('pricing')
                                        ->label('Pricing Plans')
                                        ->icon('heroicon-o-currency-dollar')
                                        ->schema(array_merge([
                                            TextInput::make('section_title')->default('Choose Your Plan'),
                                            TextInput::make('section_subtitle')->default('Transparent pricing for institutions of all sizes.'),
                                            Repeater::make('plans')
                                                ->schema([
                                                    TextInput::make('name')->label('Plan Name')->required(),
                                                    TextInput::make('price')->label('Price (e.g. $49/mo)')->required(),
                                                    TextInput::make('subtext')->label('Small Subtext (e.g. Billed annually)'),
                                                    TagsInput::make('features')->label('Plan Features'),
                                                    TextInput::make('button_label')->default('Get Started'),
                                                    TextInput::make('button_url')->default('#'),
                                                    Toggle::make('is_popular')->label('⭐ Highlight as Popular'),
                                                ])->columns(2),
                                        ], self::getDesignSchema())),

                                    Block::make('gallery')
                                        ->label('Image Gallery')
                                        ->icon('heroicon-o-photo')
                                        ->schema(array_merge([
                                            TextInput::make('section_title')->default('Screenshots'),
                                            FileUpload::make('images')->multiple()->disk(self::getStorageDisk())->directory('modules/gallery')->reorderable()->appendFiles(),
                                        ], self::getDesignSchema())),

                                    Block::make('video_playlist')
                                        ->label('Video Tutorials')
                                        ->icon('heroicon-o-play')
                                        ->schema(array_merge([
                                            TextInput::make('section_title')->default('Video Tutorials'),
                                            Repeater::make('videos')
                                                ->schema([
                                                    TextInput::make('title')->required(),
                                                    TextInput::make('youtube_id')->label('YouTube ID')->required(),
                                                    Textarea::make('description')->rows(2),
                                                ])->columns(1),
                                        ], self::getDesignSchema())),

                                    Block::make('testimonials')
                                        ->label('Testimonials')
                                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                                        ->schema(array_merge([
                                            TextInput::make('section_title')->default('What Our Clients Say'),
                                            Repeater::make('reviews')
                                                ->schema([
                                                    TextInput::make('client_name')->required(),
                                                    TextInput::make('designation')->required(),
                                                    Textarea::make('review')->required()->rows(3),
                                                    FileUpload::make('avatar')->disk(self::getStorageDisk())->directory('modules/avatars'),
                                                ])->columns(2),
                                        ], self::getDesignSchema())),

                                    Block::make('faqs')
                                        ->label('FAQs Accordion')
                                        ->icon('heroicon-o-question-mark-circle')
                                        ->schema(array_merge([
                                            TextInput::make('section_title')->default('Frequently Asked Questions'),
                                            Repeater::make('questions')
                                                ->schema([
                                                    TextInput::make('question')->required(),
                                                    Textarea::make('answer')->required()->rows(3),
                                                ])->columns(1),
                                        ], self::getDesignSchema())),

                                    Block::make('cta_banner')
                                        ->label('Call to Action Banner')
                                        ->icon('heroicon-o-megaphone')
                                        ->schema(array_merge([
                                            TextInput::make('title')->required(),
                                            Textarea::make('subtitle')->rows(2),
                                            Repeater::make('buttons')
                                                ->schema([
                                                    TextInput::make('label')->required(),
                                                    TextInput::make('url')->required(),
                                                    ColorPicker::make('button_color')->label('Custom Button Color'),
                                                ])->columns(3)->maxItems(2),
                                        ], self::getDesignSchema())),
                                ])
                                ->columnSpanFull()
                                ->collapsible()
                                ->cloneable()
                                ->reorderableWithDragAndDrop()
                        ]),
                    
                    Tab::make('⚙️ Settings')
                        ->icon('heroicon-o-cog-6-tooth')
                        ->schema([
                            TextInput::make('sort_order')->numeric()->default(0)->label('Display Order'),
                            Toggle::make('is_active')->default(true)->label('Module Active Status'),
                        ])->columns(2),
                ])
                ->columnSpanFull()
                ->contained(false),
        ]);
    }
}
