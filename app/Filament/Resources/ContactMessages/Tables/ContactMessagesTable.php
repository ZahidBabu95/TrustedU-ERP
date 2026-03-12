<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('subject')->limit(40)->toggleable(),
                TextColumn::make('status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new'      => 'danger',
                        'read'     => 'info',
                        'replied'  => 'success',
                        'archived' => 'gray',
                        default    => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Received'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived']),
            ])
            ->recordActions([EditAction::make()])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}


