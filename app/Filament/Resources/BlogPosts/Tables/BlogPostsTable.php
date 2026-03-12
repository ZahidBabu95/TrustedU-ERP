<?php

namespace App\Filament\Resources\BlogPosts\Tables;

use App\Models\BlogCategory;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class BlogPostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('featured_image')->width(60),
                TextColumn::make('title')->searchable()->limit(50)->weight('bold'),
                TextColumn::make('category.name')->badge()->toggleable(),
                TextColumn::make('status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'draft'     => 'gray',
                        'published' => 'success',
                        'archived'  => 'warning',
                        default     => 'gray',
                    }),
                TextColumn::make('published_at')->dateTime()->sortable()->toggleable(),
                TextColumn::make('updated_at')->dateTime()->sortable()->toggleable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(['draft' => 'Draft', 'published' => 'Published', 'archived' => 'Archived']),
                SelectFilter::make('blog_category_id')
                    ->label('Category')
                    ->options(BlogCategory::pluck('name', 'id')),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}


