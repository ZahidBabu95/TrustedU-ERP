<?php

namespace App\Filament\Widgets;

use App\Models\DemoRequest;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentDemoRequestsWidget extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int|string|array $columnSpan = 'full';
    protected static ?string $heading = 'Recent Demo Requests';

    public function table(Table $table): Table
    {
        return $table
            ->query(DemoRequest::query()->latest()->limit(8))
            ->columns([
                Tables\Columns\TextColumn::make('contact_name')->label('Name')->weight('bold'),
                Tables\Columns\TextColumn::make('institution_name')->label('Institution')->limit(35),
                Tables\Columns\TextColumn::make('phone'),
                Tables\Columns\TextColumn::make('district'),
                Tables\Columns\TextColumn::make('status')->badge()
                    ->colors(['warning' => 'pending', 'info' => 'contacted', 'success' => 'converted', 'danger' => 'rejected']),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->label('Requested'),
            ]);
    }
}
