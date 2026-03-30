<?php

namespace App\Filament\Resources\CrmPayments;

use App\Filament\Resources\CrmPayments\Pages\ListCrmPayments;
use App\Models\Client;
use App\Models\CrmInvoice;
use App\Models\CrmPayment;
use App\Models\User;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CrmPaymentResource extends Resource
{
    protected static ?string $model = CrmPayment::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-banknotes'; }
    public static function getNavigationGroup(): ?string { return 'CRM'; }
    public static function getNavigationLabel(): string { return 'Payments'; }
    protected static ?int $navigationSort = 8;

    public static function getNavigationBadge(): ?string
    {
        $count = CrmPayment::thisMonth()->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string { return 'success'; }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('payment_date')
                    ->label('Date')
                    ->date()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('invoice.invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->copyable()
                    ->color('primary'),
                TextColumn::make('amount')
                    ->money('BDT')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('payment_method')
                    ->label('Method')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmPayment::METHOD_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'bank_transfer' => 'primary',
                        'bkash'         => 'danger',
                        'nagad'         => 'warning',
                        'rocket'        => 'info',
                        'cash'          => 'success',
                        'cheque'        => 'gray',
                        'card'          => 'info',
                        default         => 'gray',
                    }),
                TextColumn::make('reference')
                    ->label('Reference/TrxID')
                    ->searchable()
                    ->placeholder('—')
                    ->copyable(),
                TextColumn::make('receiver.name')
                    ->label('Received By')
                    ->placeholder('—'),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('payment_method')
                    ->label('Method')
                    ->options(CrmPayment::METHOD_LABELS)
                    ->multiple(),
                SelectFilter::make('client_id')
                    ->label('Client')
                    ->options(Client::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->defaultSort('payment_date', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListCrmPayments::route('/'),
        ];
    }
}
