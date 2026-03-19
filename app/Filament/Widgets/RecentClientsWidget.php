<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;

class RecentClientsWidget extends TableWidget
{
    protected int|string|array $columnSpan = 'full';
    protected static ?int $sort = 4;
    protected static ?string $heading = 'Recent Clients / Institutes';

    public function table(Table $table): Table
    {
        return $table
            ->query(fn (): Builder => Client::query()->latest()->limit(8))
            ->columns([
                Tables\Columns\ImageColumn::make('logo')
                    ->label('Logo')
                    ->circular()
                    ->defaultImageUrl(fn () => 'https://ui-avatars.com/api/?background=3b82f6&color=fff&name=C'),

                Tables\Columns\TextColumn::make('name')
                    ->label('Institute')
                    ->searchable()
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('contact_person')
                    ->label('Contact')
                    ->default('—'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->sortable(),
            ]);
    }
}
