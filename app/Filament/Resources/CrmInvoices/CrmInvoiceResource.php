<?php

namespace App\Filament\Resources\CrmInvoices;

use App\Filament\Resources\CrmInvoices\Pages\CreateCrmInvoice;
use App\Filament\Resources\CrmInvoices\Pages\EditCrmInvoice;
use App\Filament\Resources\CrmInvoices\Pages\ListCrmInvoices;
use App\Filament\Resources\CrmInvoices\Pages\ViewCrmInvoice;
use App\Models\Client;
use App\Models\CrmActivity;
use App\Models\CrmInvoice;
use App\Models\CrmPayment;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CrmInvoiceResource extends Resource
{
    protected static ?string $model = CrmInvoice::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-document-currency-bangladeshi'; }
    public static function getNavigationGroup(): ?string { return 'CRM'; }
    public static function getNavigationLabel(): string { return 'Invoices'; }
    protected static ?int $navigationSort = 7;

    public static function getNavigationBadge(): ?string
    {
        $overdue = CrmInvoice::overdue()->count();
        return $overdue > 0 ? (string) $overdue : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return CrmInvoice::overdue()->count() > 0 ? 'danger' : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Invoice')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([

                    // ━━ TAB 1: Invoice Info ━━
                    Tab::make('Invoice Info')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Invoice Details')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('invoice_number')
                                        ->label('Invoice #')
                                        ->disabled()
                                        ->dehydrated()
                                        ->placeholder('Auto-generated')
                                        ->prefixIcon('heroicon-o-hashtag'),
                                    TextInput::make('title')
                                        ->label('Invoice Title')
                                        ->required()
                                        ->default('Service Invoice')
                                        ->placeholder('e.g. ERP Implementation Invoice')
                                        ->prefixIcon('heroicon-o-document-text'),
                                    Select::make('client_id')
                                        ->label('Client')
                                        ->options(Client::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if ($state) {
                                                $client = Client::find($state);
                                                if ($client) {
                                                    $set('client_name', $client->name);
                                                    $set('client_address', $client->address);
                                                    $set('client_phone', $client->phone);
                                                    $set('client_email', $client->email);
                                                }
                                            }
                                        })
                                        ->prefixIcon('heroicon-o-building-office-2'),
                                    Select::make('status')
                                        ->options(CrmInvoice::STATUS_LABELS)
                                        ->default('draft')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-signal'),
                                    DatePicker::make('issue_date')
                                        ->label('Issue Date')
                                        ->default(now())
                                        ->native(false)
                                        ->required(),
                                    DatePicker::make('due_date')
                                        ->label('Due Date')
                                        ->default(now()->addDays(15))
                                        ->native(false)
                                        ->required(),
                                ]),
                        ]),

                    // ━━ TAB 2: Company & Client Info ━━
                    Tab::make('Company & Client')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Section::make('Company Info (Invoice Header)')
                                ->columns(2)
                                ->description('ইনভয়েসে কোম্পানির তথ্য')
                                ->schema([
                                    TextInput::make('company_name')
                                        ->default('TrustedU Technologies')
                                        ->prefixIcon('heroicon-o-building-office'),
                                    TextInput::make('company_phone')
                                        ->default('+880 1700-000000')
                                        ->prefixIcon('heroicon-o-phone'),
                                    TextInput::make('company_email')
                                        ->email()
                                        ->default('info@trustedu.com.bd')
                                        ->prefixIcon('heroicon-o-envelope'),
                                    TextInput::make('company_address')
                                        ->columnSpanFull()
                                        ->default('Dhaka, Bangladesh')
                                        ->prefixIcon('heroicon-o-map-pin'),
                                ]),
                            Section::make('Client Info (Bill To)')
                                ->columns(2)
                                ->description('ক্লায়েন্টের তথ্য ইনভয়েসে দেখাবে')
                                ->schema([
                                    TextInput::make('client_name')
                                        ->prefixIcon('heroicon-o-user'),
                                    TextInput::make('client_phone')
                                        ->prefixIcon('heroicon-o-phone'),
                                    TextInput::make('client_email')
                                        ->email()
                                        ->prefixIcon('heroicon-o-envelope'),
                                    TextInput::make('client_address')
                                        ->columnSpanFull()
                                        ->prefixIcon('heroicon-o-map-pin'),
                                ]),
                        ]),

                    // ━━ TAB 3: Line Items ━━
                    Tab::make('Items')
                        ->icon('heroicon-o-queue-list')
                        ->schema([
                            Section::make('Invoice Items')
                                ->schema([
                                    Repeater::make('items')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('description')
                                                ->required()
                                                ->placeholder('Item / Service Description')
                                                ->columnSpan(3),
                                            TextInput::make('qty')
                                                ->numeric()
                                                ->default(1)
                                                ->minValue(1)
                                                ->label('Qty'),
                                            TextInput::make('rate')
                                                ->numeric()
                                                ->prefix('৳')
                                                ->required()
                                                ->default(0)
                                                ->label('Rate'),
                                            TextInput::make('amount')
                                                ->numeric()
                                                ->prefix('৳')
                                                ->disabled()
                                                ->dehydrated()
                                                ->label('Amount'),
                                        ])
                                        ->columns(6)
                                        ->defaultItems(1)
                                        ->addActionLabel('+ Add Item')
                                        ->columnSpanFull(),
                                ]),
                            Section::make('Totals & Discounts')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('tax_percent')
                                        ->numeric()
                                        ->suffix('%')
                                        ->default(0)
                                        ->label('Tax / VAT %'),
                                    TextInput::make('discount_amount')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->default(0)
                                        ->label('Discount Amount'),
                                ]),
                        ]),

                    // ━━ TAB 4: Payment & Notes ━━
                    Tab::make('Payment & Notes')
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            Section::make('Payment Info')
                                ->columns(2)
                                ->schema([
                                    Select::make('payment_method')
                                        ->label('Preferred Payment Method')
                                        ->options(CrmInvoice::PAYMENT_METHOD_LABELS)
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-credit-card'),
                                    Select::make('billing_plan_id')
                                        ->label('Billing Plan')
                                        ->relationship('billingPlan', 'plan_name')
                                        ->placeholder('— Direct Invoice —')
                                        ->searchable()
                                        ->preload(),
                                ]),
                            Section::make('Notes')
                                ->schema([
                                    Textarea::make('notes')
                                        ->label('Internal Notes')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->placeholder('Internal notes...'),
                                    Textarea::make('terms_conditions')
                                        ->label('Terms & Conditions')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->default("1. Payment is due within 15 days of invoice date.\n2. Late payments may incur additional charges.\n3. This is a computer-generated invoice.")
                                        ->placeholder('ইনভয়েসের নিচে Terms & Conditions...'),
                                ]),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->label('Invoice #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->copyable(),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->description(fn ($record) => $record->client?->client_id ?? ''),
                TextColumn::make('title')
                    ->label('Title')
                    ->searchable()
                    ->lineClamp(1)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmInvoice::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'draft'          => 'gray',
                        'sent'           => 'info',
                        'paid'           => 'success',
                        'partially_paid' => 'warning',
                        'overdue'        => 'danger',
                        'cancelled'      => 'gray',
                        default          => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft'          => 'heroicon-o-pencil',
                        'sent'           => 'heroicon-o-paper-airplane',
                        'paid'           => 'heroicon-o-check-circle',
                        'partially_paid' => 'heroicon-o-clock',
                        'overdue'        => 'heroicon-o-exclamation-triangle',
                        'cancelled'      => 'heroicon-o-x-circle',
                        default          => 'heroicon-o-document',
                    }),
                TextColumn::make('total')
                    ->label('Total')
                    ->formatStateUsing(fn ($state) => '৳' . number_format($state, 2))
                    ->sortable()
                    ->weight('bold')
                    ->color('primary'),
                TextColumn::make('paid_amount')
                    ->label('Paid')
                    ->formatStateUsing(fn ($state) => '৳' . number_format($state, 2))
                    ->color('success'),
                TextColumn::make('due_amount')
                    ->label('Due')
                    ->getStateUsing(fn ($record) => $record->due_amount)
                    ->formatStateUsing(fn ($state) => '৳' . number_format($state, 2))
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success'),
                TextColumn::make('issue_date')
                    ->label('Issued')
                    ->date('d M Y')
                    ->sortable(),
                TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('d M Y')
                    ->sortable()
                    ->color(fn ($record) =>
                        $record->due_date && $record->due_date->isPast() && !$record->isFullyPaid()
                            ? 'danger' : null
                    ),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CrmInvoice::STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('client_id')
                    ->label('Client')
                    ->options(Client::pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // ★ Print Invoice
                Action::make('print')
                    ->label('🖨️ Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (CrmInvoice $r) => route('crm.invoice.print', $r->id), shouldOpenInNewTab: true),

                // ★ Send Invoice
                Action::make('send')
                    ->label('📧 Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->action(function (CrmInvoice $record) {
                        $record->calculateTotals();
                        $record->update(['status' => 'sent']);

                        CrmActivity::log('client', $record->client_id, 'note',
                            "Invoice {$record->invoice_number} sent",
                            "Amount: ৳" . number_format($record->total, 2)
                        );

                        Notification::make()
                            ->title("📧 Invoice {$record->invoice_number} Sent!")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmInvoice $r) => $r->status === 'draft'),

                // ★ Record Payment
                Action::make('recordPayment')
                    ->label('💰 Payment')
                    ->icon('heroicon-o-banknotes')
                    ->color('success')
                    ->form([
                        TextInput::make('amount')
                            ->numeric()
                            ->prefix('৳')
                            ->required()
                            ->label('Payment Amount')
                            ->default(fn (CrmInvoice $record) => $record->due_amount),
                        Select::make('payment_method')
                            ->options(CrmPayment::METHOD_LABELS)
                            ->default('bank_transfer')
                            ->native(false)
                            ->required(),
                        TextInput::make('reference')
                            ->label('Reference / TrxID')
                            ->placeholder('Transaction reference'),
                        DatePicker::make('payment_date')
                            ->default(now())
                            ->native(false)
                            ->required(),
                    ])
                    ->action(function (CrmInvoice $record, array $data) {
                        CrmPayment::create([
                            'client_id'      => $record->client_id,
                            'invoice_id'     => $record->id,
                            'amount'         => $data['amount'],
                            'payment_method' => $data['payment_method'],
                            'reference'      => $data['reference'] ?? null,
                            'payment_date'   => $data['payment_date'],
                            'received_by'    => auth()->id(),
                            'notes'          => "Payment for {$record->invoice_number}",
                        ]);

                        $record->recordPayment((float)$data['amount']);

                        CrmActivity::log('client', $record->client_id, 'note',
                            "💰 Payment ৳" . number_format($data['amount'], 2) . " received for {$record->invoice_number}",
                        );

                        Notification::make()
                            ->title('💰 Payment Recorded!')
                            ->body("৳" . number_format($data['amount'], 2) . " received for {$record->invoice_number}")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmInvoice $r) => !$r->isFullyPaid() && $r->status !== 'cancelled'),

                // Mark Overdue
                Action::make('markOverdue')
                    ->label('⚠️ Overdue')
                    ->icon('heroicon-o-exclamation-triangle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (CrmInvoice $record) {
                        $record->update(['status' => 'overdue']);
                        Notification::make()->title('Invoice Marked Overdue')->warning()->send();
                    })
                    ->visible(fn (CrmInvoice $r) =>
                        in_array($r->status, ['sent', 'partially_paid'])
                        && $r->due_date && $r->due_date->isPast()
                    ),
            ])
            ->headerActions([
                CreateAction::make()
                    ->label('+ New Invoice')
                    ->icon('heroicon-o-plus'),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCrmInvoices::route('/'),
            'create' => CreateCrmInvoice::route('/create'),
            'view'   => ViewCrmInvoice::route('/{record}'),
            'edit'   => EditCrmInvoice::route('/{record}/edit'),
        ];
    }
}
