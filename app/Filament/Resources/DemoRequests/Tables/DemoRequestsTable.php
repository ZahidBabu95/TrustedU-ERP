<?php

namespace App\Filament\Resources\DemoRequests\Tables;

use App\Models\DemoRequest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DemoRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact_name')->searchable()->weight('bold'),
                TextColumn::make('institution_name')->searchable()->limit(35),
                TextColumn::make('institution_type')->badge(),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('district'),
                TextColumn::make('status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'contacted' => 'info',
                        'demo_done' => 'primary',
                        'converted' => 'success',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Requested'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(['pending' => 'Pending', 'contacted' => 'Contacted', 'demo_done' => 'Demo Done', 'converted' => 'Converted', 'rejected' => 'Rejected']),
                SelectFilter::make('institution_type')
                    ->options(['school' => 'School', 'college' => 'College', 'university' => 'University', 'madrasha' => 'Madrasha']),
            ])
            ->recordActions([
                EditAction::make(),
                Action::make('markContacted')
                    ->label('Mark Contacted')
                    ->icon('heroicon-o-phone')
                    ->color('info')
                    ->action(fn(DemoRequest $record) => $record->update(['status' => 'contacted']))
                    ->visible(fn(DemoRequest $record) => $record->status === 'pending'),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}


