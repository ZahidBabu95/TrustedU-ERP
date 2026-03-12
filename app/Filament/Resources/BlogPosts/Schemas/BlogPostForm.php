<?php

namespace App\Filament\Resources\BlogPosts\Schemas;

use App\Models\BlogCategory;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;
use Filament\Schemas\Components\Utilities\Set;

class BlogPostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('Post Content')->schema([
                TextInput::make('title')->required()->maxLength(255)->live(onBlur: true)
                    ->afterStateUpdated(fn($state, Set $set) => $set('slug', Str::slug($state))),
                TextInput::make('slug')->required()->maxLength(255),
                Select::make('blog_category_id')
                    ->label('Category')
                    ->options(BlogCategory::pluck('name', 'id'))
                    ->searchable()
                    ->createOptionForm([
                        TextInput::make('name')->required(),
                        TextInput::make('slug')->required(),
                    ])
                    ->createOptionUsing(fn(array $data) => BlogCategory::create($data)->id),
                Select::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived'])
                    ->default('draft'),
                Textarea::make('excerpt')->rows(2)->columnSpanFull(),
                RichEditor::make('body')->required()->columnSpanFull()
                    ->toolbarButtons(['bold', 'italic', 'underline', 'link', 'bulletList', 'orderedList', 'h2', 'h3', 'blockquote', 'codeBlock']),
            ])->columns(2),

            Section::make('Media & SEO')->schema([
                FileUpload::make('featured_image')->image()->directory('blog')->columnSpanFull(),
                TagsInput::make('tags')->columnSpanFull(),
                DateTimePicker::make('published_at'),
                TextInput::make('meta_title')->maxLength(255),
                Textarea::make('meta_description')->rows(2)->columnSpanFull(),
            ])->columns(2)->collapsed(),
        ]);
    }
}
