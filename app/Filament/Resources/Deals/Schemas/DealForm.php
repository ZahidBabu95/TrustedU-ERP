<?php

namespace App\Filament\Resources\Deals\Schemas;

use App\Models\Client;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\User;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;

class DealForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Deed')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([

                    // ━━ TAB 1: Agreement Info ━━
                    Tab::make('📜 Agreement Info')
                        ->icon('heroicon-o-document-check')
                        ->schema([
                            Section::make('Deed / চুক্তিপত্র')
                                ->description('চুক্তিপত্রের মূল তথ্যসমূহ')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('deed_number')
                                        ->label('Deed Number')
                                        ->disabled()
                                        ->dehydrated()
                                        ->prefixIcon('heroicon-o-hashtag')
                                        ->placeholder('Auto-generated'),
                                    Select::make('deed_status')
                                        ->label('Deed Status')
                                        ->options(Deal::DEED_STATUS_LABELS)
                                        ->default('not_created')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-signal'),
                                    TextInput::make('title')
                                        ->label('Agreement Title')
                                        ->required()
                                        ->maxLength(255)
                                        ->placeholder('e.g. 2-Year Software Service Agreement')
                                        ->prefixIcon('heroicon-o-document-text'),
                                    TextInput::make('company')
                                        ->label('Client / Institute Name')
                                        ->maxLength(255)
                                        ->placeholder('প্রতিষ্ঠানের নাম')
                                        ->prefixIcon('heroicon-o-building-office'),
                                    DatePicker::make('deed_effective_date')
                                        ->label('Agreement Start Date')
                                        ->default(now())
                                        ->native(false),
                                    DatePicker::make('deed_end_date')
                                        ->label('Agreement End Date')
                                        ->default(now()->addYears(2))
                                        ->native(false),
                                    TextInput::make('deed_duration')
                                        ->label('Duration (Years)')
                                        ->default('2')
                                        ->placeholder('e.g. 1, 2, 3')
                                        ->prefixIcon('heroicon-o-clock'),
                                    Select::make('lead_id')
                                        ->label('Linked Lead')
                                        ->options(Lead::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('— Select Lead —')
                                        ->prefixIcon('heroicon-o-link'),
                                    Select::make('client_id')
                                        ->label('Linked Client')
                                        ->options(Client::pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->placeholder('— Select Client —')
                                        ->prefixIcon('heroicon-o-building-office-2'),
                                    Select::make('assigned_to')
                                        ->label('Assigned To')
                                        ->options(User::where('is_active', true)->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-user-circle'),
                                ]),
                        ]),

                    // ━━ TAB 2: Client Info ━━
                    Tab::make('Client Info')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Section::make('Client / ক্লায়েন্ট প্রতিনিধি')
                                ->description('চুক্তিপত্রে ক্লায়েন্টের তথ্য')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('deed_client_representative')
                                        ->label('Representative Name')
                                        ->placeholder('e.g. Md. Bashir Uddin')
                                        ->prefixIcon('heroicon-o-user'),
                                    TextInput::make('deed_client_designation')
                                        ->label('Designation')
                                        ->default('Principal')
                                        ->placeholder('e.g. Principal, Chairman, Head Master')
                                        ->prefixIcon('heroicon-o-identification'),
                                    TextInput::make('contact_email')
                                        ->label('Email')
                                        ->email()
                                        ->maxLength(255)
                                        ->prefixIcon('heroicon-o-envelope'),
                                    TextInput::make('contact_phone')
                                        ->label('Phone')
                                        ->tel()
                                        ->maxLength(30)
                                        ->prefixIcon('heroicon-o-phone'),
                                    Textarea::make('deed_client_address')
                                        ->label('Client Address')
                                        ->rows(2)
                                        ->columnSpanFull()
                                        ->placeholder('প্রতিষ্ঠানের সম্পূর্ণ ঠিকানা...'),
                                ]),
                        ]),

                    // ━━ TAB 3: Plan & Pricing ━━
                    Tab::make('Plan & Pricing')
                        ->icon('heroicon-o-banknotes')
                        ->schema([
                            Section::make('Plan Details / প্যাকেজ')
                                ->description('সাবস্ক্রিপশন প্ল্যান ও মূল্যায়ন')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('deed_plan_name')
                                        ->label('Plan Name')
                                        ->default('Starter Plan')
                                        ->placeholder('e.g. Starter Plan, Economy Plan, Plus Plan')
                                        ->prefixIcon('heroicon-o-rectangle-stack'),
                                    TextInput::make('value')
                                        ->label('Total Deal Value (৳)')
                                        ->numeric()
                                        ->prefix('৳'),
                                    TextInput::make('deed_monthly_fee')
                                        ->label('Monthly Fee (৳)')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->default(0),
                                    TextInput::make('deed_per_user_rate')
                                        ->label('Per User Rate (৳/month)')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->default(15),
                                    TextInput::make('deed_installation_cost')
                                        ->label('Installation Cost (৳)')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->default(4000),
                                    TextInput::make('deed_total_users')
                                        ->label('Total Users')
                                        ->numeric()
                                        ->placeholder('Number of users'),
                                ]),

                            Section::make('Plan Features / ফিচার সমূহ')
                                ->description('চুক্তিপত্রে অন্তর্ভুক্ত ফিচারের তালিকা')
                                ->collapsible()
                                ->schema([
                                    Repeater::make('deed_plan_features')
                                        ->label('')
                                        ->simple(
                                            TextInput::make('feature')
                                                ->placeholder('Feature description...')
                                        )
                                        ->defaultItems(0)
                                        ->addActionLabel('+ Add Feature')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ━━ TAB 4: Bank & Payment ━━
                    Tab::make('Bank & Payment')
                        ->icon('heroicon-o-credit-card')
                        ->schema([
                            Section::make('Bank Accounts / ব্যাংক তথ্য')
                                ->description('চুক্তিপত্রের Appendix-এ ব্যাংক অ্যাকাউন্ট তথ্য')
                                ->schema([
                                    Repeater::make('deed_bank_accounts')
                                        ->label('')
                                        ->schema([
                                            TextInput::make('bank_name')
                                                ->label('Bank Name')
                                                ->required()
                                                ->placeholder('e.g. Dutch Bangla Bank Limited'),
                                            TextInput::make('account_name')
                                                ->label('A/C Name')
                                                ->required()
                                                ->placeholder('Account holder name'),
                                            TextInput::make('account_number')
                                                ->label('A/C Number')
                                                ->required()
                                                ->placeholder('Account number'),
                                            TextInput::make('branch')
                                                ->label('Branch')
                                                ->placeholder('Branch name'),
                                        ])
                                        ->columns(2)
                                        ->defaultItems(0)
                                        ->addActionLabel('+ Add Bank Account')
                                        ->columnSpanFull(),
                                ]),
                        ]),

                    // ━━ TAB 5: Company Info ━━
                    Tab::make('Company Info')
                        ->icon('heroicon-o-building-office')
                        ->schema([
                            Section::make('Provider Company / কোম্পানি তথ্য')
                                ->description('চুক্তিপত্রের হেডারে কোম্পানির তথ্য — ডিফল্ট ভ্যালু ব্যবহার হবে যদি খালি থাকে')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('deed_company_info.name')
                                        ->label('Company Name')
                                        ->placeholder('Amar School Management Software Company'),
                                    TextInput::make('deed_company_info.tagline')
                                        ->label('Tagline')
                                        ->placeholder('Manage School Easily'),
                                    TextInput::make('deed_company_info.phone')
                                        ->label('Phone')
                                        ->placeholder('+88 01793661417'),
                                    TextInput::make('deed_company_info.email')
                                        ->label('Email')
                                        ->placeholder('hello.amarschool@gmail.com'),
                                    TextInput::make('deed_company_info.website')
                                        ->label('Website')
                                        ->placeholder('www.amarschool.co'),
                                    TextInput::make('deed_company_info.ceo_name')
                                        ->label('CEO Name')
                                        ->placeholder('Md. Aminul Islam'),
                                    TextInput::make('deed_company_info.ceo_title')
                                        ->label('CEO Title')
                                        ->placeholder('CEO'),
                                    TextInput::make('deed_company_info.product_name')
                                        ->label('Product Name')
                                        ->placeholder('Amar School'),
                                    Textarea::make('deed_company_info.address')
                                        ->label('Company Address')
                                        ->rows(2)
                                        ->columnSpanFull()
                                        ->placeholder('House #192, Road #2, Avenue #3, Mirpur DOHS, Dhaka 1216'),
                                ]),
                        ]),

                    // ━━ TAB 6: Notes ━━
                    Tab::make('Notes')
                        ->icon('heroicon-o-chat-bubble-bottom-center-text')
                        ->schema([
                            Section::make('Notes')
                                ->schema([
                                    Textarea::make('deed_notes')
                                        ->label('Deed Notes / চুক্তিপত্র নোট')
                                        ->rows(4)
                                        ->columnSpanFull()
                                        ->placeholder('চুক্তিপত্র সংক্রান্ত অতিরিক্ত নোট বা বিশেষ শর্ত...'),
                                    Textarea::make('notes')
                                        ->label('General Notes')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ]);
    }
}
