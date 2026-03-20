<?php

namespace App\Filament\Resources\Clients\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Forms;
use Filament\Schemas\Schema;
use Filament\Actions;
use Filament\Tables;
use Filament\Tables\Table;

class MonthlyStatsRelationManager extends RelationManager
{
    protected static string $relationship = 'monthlyStats';
    protected static ?string $title = 'Monthly Student Stats';
    protected static string|\BackedEnum|null $icon = 'heroicon-o-academic-cap';

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            Forms\Components\Select::make('year')
                ->label('Year')
                ->options(array_combine(
                    range(date('Y'), date('Y') - 5),
                    range(date('Y'), date('Y') - 5)
                ))
                ->required()
                ->native(false),
            Forms\Components\Select::make('month')
                ->label('Month')
                ->options([
                    1 => 'January', 2 => 'February', 3 => 'March',
                    4 => 'April', 5 => 'May', 6 => 'June',
                    7 => 'July', 8 => 'August', 9 => 'September',
                    10 => 'October', 11 => 'November', 12 => 'December',
                ])
                ->required()
                ->native(false),
            Forms\Components\TextInput::make('active_students')
                ->label('Active Students')
                ->numeric()
                ->required()
                ->minValue(0),
            Forms\Components\TextInput::make('total_students')
                ->label('Total Students')
                ->numeric()
                ->required()
                ->minValue(0),
            Forms\Components\Textarea::make('notes')
                ->label('Notes')
                ->rows(2)
                ->columnSpanFull(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('year')
                    ->label('Year')
                    ->sortable(),
                Tables\Columns\TextColumn::make('month_name')
                    ->label('Month')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('active_students')
                    ->label('Active Students')
                    ->numeric()
                    ->badge()
                    ->color('success')
                    ->sortable(),
                Tables\Columns\TextColumn::make('total_students')
                    ->label('Total Students')
                    ->numeric()
                    ->badge()
                    ->color('info')
                    ->sortable(),
                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('year', 'desc')
            ->filters([
                Tables\Filters\SelectFilter::make('year')
                    ->options(array_combine(
                        range(date('Y'), date('Y') - 5),
                        range(date('Y'), date('Y') - 5)
                    )),
            ])
            ->headerActions([
                Actions\CreateAction::make()
                    ->icon('heroicon-o-plus'),
            ])
            ->recordActions([
                Actions\EditAction::make(),
                Actions\DeleteAction::make(),
            ])
            ->toolbarActions([
                Actions\BulkActionGroup::make([
                    Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
