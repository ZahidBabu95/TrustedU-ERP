<?php

namespace App\Filament\Resources\CrmBillingPlans;

use App\Filament\Resources\CrmBillingPlans\Pages\CreateCrmBillingPlan;
use App\Filament\Resources\CrmBillingPlans\Pages\EditCrmBillingPlan;
use App\Filament\Resources\CrmBillingPlans\Pages\ListCrmBillingPlans;
use App\Models\Client;
use App\Models\CrmActivity;
use App\Models\CrmBillingPlan;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class CrmBillingPlanResource extends Resource
{
    protected static ?string $model = CrmBillingPlan::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-credit-card'; }
    public static function getNavigationGroup(): ?string { return 'CRM'; }
    public static function getNavigationLabel(): string { return 'Billing Plans'; }
    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        $due = CrmBillingPlan::dueSoon(7)->count();
        return $due > 0 ? (string) $due : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return CrmBillingPlan::dueSoon(3)->count() > 0 ? 'danger' : 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('BillingPlan')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([

                    // ━━ TAB 1: Plan Info ━━
                    Tab::make('Plan Info')
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            Section::make('Billing Plan')
                                ->columns(2)
                                ->schema([
                                    Select::make('client_id')
                                        ->label('Client')
                                        ->options(Client::where('is_active', true)->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->required()
                                        ->prefixIcon('heroicon-o-building-office-2'),
                                    TextInput::make('plan_name')
                                        ->label('Plan Name')
                                        ->required()
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-tag')
                                        ->placeholder('e.g. Basic ERP, Premium ERP'),
                                    Select::make('billing_type')
                                        ->options(CrmBillingPlan::BILLING_TYPE_LABELS)
                                        ->default('prepaid')
                                        ->native(false)
                                        ->required(),
                                    Select::make('frequency')
                                        ->options(CrmBillingPlan::FREQUENCY_LABELS)
                                        ->default('monthly')
                                        ->native(false)
                                        ->required(),
                                    Toggle::make('is_active')
                                        ->label('Active')
                                        ->default(true),
                                    Toggle::make('auto_renew')
                                        ->label('Auto Renew')
                                        ->default(true),
                                ]),
                        ]),

                    // ━━ TAB 2: Pricing ━━
                    Tab::make('Pricing')
                        ->icon('heroicon-o-calculator')
                        ->schema([
                            Section::make('Base Pricing')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('base_amount')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, $get, $set) {
                                            $addons = collect($get('addons') ?? []);
                                            $addonsTotal = $addons->sum('price');
                                            $set('total_amount', round(floatval($state) + $addonsTotal, 2));
                                        }),
                                    TextInput::make('total_amount')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->disabled()
                                        ->dehydrated()
                                        ->label('Total Amount'),
                                ]),

                            Section::make('Add-ons')
                                ->description('অতিরিক্ত সার্ভিস/মডিউল')
                                ->schema([
                                    Repeater::make('addons')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('name')
                                                ->label('Add-on Name')
                                                ->required()
                                                ->placeholder('e.g. SMS Pack, Extra Storage'),
                                            TextInput::make('price')
                                                ->numeric()
                                                ->prefix('৳')
                                                ->required()
                                                ->default(0),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel('+ Add Add-on')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ━━ TAB 3: Schedule ━━
                    Tab::make('Schedule')
                        ->icon('heroicon-o-calendar')
                        ->schema([
                            Section::make('Billing Schedule')
                                ->columns(2)
                                ->schema([
                                    DatePicker::make('start_date')
                                        ->label('Start Date')
                                        ->native(false)
                                        ->default(now())
                                        ->required(),
                                    DatePicker::make('end_date')
                                        ->label('End Date')
                                        ->native(false),
                                    DatePicker::make('next_billing_date')
                                        ->label('Next Billing Date')
                                        ->native(false)
                                        ->default(now()->addMonth())
                                        ->required(),
                                ]),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('client.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('plan_name')
                    ->label('Plan')
                    ->searchable(),
                TextColumn::make('billing_type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmBillingPlan::BILLING_TYPE_LABELS[$state] ?? $state),
                TextColumn::make('frequency')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmBillingPlan::FREQUENCY_LABELS[$state] ?? $state)
                    ->color('primary'),
                TextColumn::make('base_amount')
                    ->money('BDT')
                    ->label('Base')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->money('BDT')
                    ->label('Total')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                IconColumn::make('auto_renew')
                    ->label('Renew')
                    ->boolean()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('next_billing_date')
                    ->label('Next Bill')
                    ->date()
                    ->sortable()
                    ->color(fn ($record) =>
                        $record->next_billing_date && $record->next_billing_date->lte(now()->addDays(7))
                            ? 'danger' : null
                    ),
                TextColumn::make('invoices_count')
                    ->label('Invoices')
                    ->counts('invoices'),
            ])
            ->filters([
                TernaryFilter::make('is_active')
                    ->label('Active'),
                SelectFilter::make('frequency')
                    ->options(CrmBillingPlan::FREQUENCY_LABELS),
                SelectFilter::make('billing_type')
                    ->options(CrmBillingPlan::BILLING_TYPE_LABELS),
            ])
            ->recordActions([
                EditAction::make(),

                // ★ Generate Invoice
                Action::make('generateInvoice')
                    ->label('📄 Invoice')
                    ->icon('heroicon-o-document-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Invoice from Billing Plan?')
                    ->modalDescription(fn (CrmBillingPlan $record) =>
                        "Invoice for ৳" . number_format($record->total_amount, 2) .
                        " ({$record->plan_name}) will be created."
                    )
                    ->action(function (CrmBillingPlan $record) {
                        $invoice = $record->invoices()->create([
                            'client_id'   => $record->client_id,
                            'items'       => [
                                ['description' => $record->plan_name, 'amount' => $record->base_amount],
                                ...collect($record->addons ?? [])->map(fn ($a) => [
                                    'description' => $a['name'] ?? 'Add-on',
                                    'amount'      => $a['price'] ?? 0,
                                ])->toArray(),
                            ],
                            'subtotal'    => $record->total_amount,
                            'total'       => $record->total_amount,
                            'tax_percent' => 0,
                            'tax_amount'  => 0,
                            'discount_amount' => 0,
                            'paid_amount' => 0,
                            'status'      => 'draft',
                            'issue_date'  => now(),
                            'due_date'    => now()->addDays(15),
                            'created_by'  => auth()->id(),
                        ]);

                        // Advance next billing date
                        $record->update([
                            'next_billing_date' => $record->getNextBillingDate(),
                        ]);

                        CrmActivity::log('client', $record->client_id, 'note',
                            "Invoice {$invoice->invoice_number} generated from billing plan",
                            "Amount: ৳{$record->total_amount}",
                            ['invoice_id' => $invoice->id, 'billing_plan_id' => $record->id]
                        );

                        Notification::make()
                            ->title("📄 Invoice {$invoice->invoice_number} Created!")
                            ->body("Amount: ৳" . number_format($record->total_amount, 2))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmBillingPlan $record) => $record->is_active),

                // Deactivate
                Action::make('deactivate')
                    ->label('Deactivate')
                    ->icon('heroicon-o-pause')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (CrmBillingPlan $record) {
                        $record->update(['is_active' => false]);
                        Notification::make()->title('Plan Deactivated')->warning()->send();
                    })
                    ->visible(fn (CrmBillingPlan $record) => $record->is_active),
            ])
            ->defaultSort('next_billing_date', 'asc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCrmBillingPlans::route('/'),
            'create' => CreateCrmBillingPlan::route('/create'),
            'edit'   => EditCrmBillingPlan::route('/{record}/edit'),
        ];
    }
}
