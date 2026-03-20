<?php

namespace App\Filament\Resources\Clients\Schemas;

use App\Models\Team;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\CheckboxList;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class ClientForm
{
    public static function getStorageDisk(): string
    {
        try {
            $provider = \App\Models\SystemSetting::where('key', 'storage_provider')->value('value');
            return ($provider === 'r2' || $provider === 's3') ? $provider : 'public';
        } catch (\Exception $e) {
            return 'public';
        }
    }

    public static function configure(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Client')
                ->tabs([

                    // ━━ TAB 1: General Info ━━
                    Tab::make('General')
                        ->icon('heroicon-o-building-office-2')
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 2])
                                ->schema([
                                    // Left: Identity
                                    Section::make('Identity')
                                        ->schema([
                                            TextInput::make('client_id')
                                                ->label('Client ID')
                                                ->placeholder('Auto-generated')
                                                ->maxLength(20)
                                                ->helperText('Leave empty for auto-generate'),
                                            TextInput::make('name')
                                                ->label('Institute / Client Name')
                                                ->required()
                                                ->maxLength(255),
                                            Select::make('institution_type')
                                                ->label('Institution Type')
                                                ->options([
                                                    'school'            => 'School',
                                                    'college'           => 'College',
                                                    'school_and_college' => 'School & College',
                                                    'university'        => 'University',
                                                    'madrasha'          => 'Madrasha',
                                                    'coaching'          => 'Coaching Center',
                                                    'corporate'         => 'Corporate',
                                                    'ngo'               => 'NGO',
                                                    'other'             => 'Other',
                                                ])->native(false)
                                                ->searchable(),
                                            Select::make('district')
                                                ->label('District / Location')
                                                ->options([
                                                    'Bagerhat' => 'Bagerhat',
                                                    'Bandarban' => 'Bandarban',
                                                    'Barguna' => 'Barguna',
                                                    'Barisal' => 'Barisal',
                                                    'Bhola' => 'Bhola',
                                                    'Bogra' => 'Bogra',
                                                    'Brahmanbaria' => 'Brahmanbaria',
                                                    'Chandpur' => 'Chandpur',
                                                    'Chapainawabganj' => 'Chapainawabganj',
                                                    'Chattogram' => 'Chattogram',
                                                    'Chuadanga' => 'Chuadanga',
                                                    'Comilla' => 'Comilla',
                                                    'Cox\'s Bazar' => 'Cox\'s Bazar',
                                                    'Dhaka' => 'Dhaka',
                                                    'Dinajpur' => 'Dinajpur',
                                                    'Faridpur' => 'Faridpur',
                                                    'Feni' => 'Feni',
                                                    'Gaibandha' => 'Gaibandha',
                                                    'Gazipur' => 'Gazipur',
                                                    'Gopalganj' => 'Gopalganj',
                                                    'Habiganj' => 'Habiganj',
                                                    'Jamalpur' => 'Jamalpur',
                                                    'Jessore' => 'Jessore',
                                                    'Jhalokati' => 'Jhalokati',
                                                    'Jhenaidah' => 'Jhenaidah',
                                                    'Joypurhat' => 'Joypurhat',
                                                    'Khagrachari' => 'Khagrachari',
                                                    'Khulna' => 'Khulna',
                                                    'Kishoreganj' => 'Kishoreganj',
                                                    'Kurigram' => 'Kurigram',
                                                    'Kushtia' => 'Kushtia',
                                                    'Lakshmipur' => 'Lakshmipur',
                                                    'Lalmonirhat' => 'Lalmonirhat',
                                                    'Madaripur' => 'Madaripur',
                                                    'Magura' => 'Magura',
                                                    'Manikganj' => 'Manikganj',
                                                    'Meherpur' => 'Meherpur',
                                                    'Moulvibazar' => 'Moulvibazar',
                                                    'Munshiganj' => 'Munshiganj',
                                                    'Mymensingh' => 'Mymensingh',
                                                    'Naogaon' => 'Naogaon',
                                                    'Narail' => 'Narail',
                                                    'Narayanganj' => 'Narayanganj',
                                                    'Narsingdi' => 'Narsingdi',
                                                    'Natore' => 'Natore',
                                                    'Nawabganj' => 'Nawabganj',
                                                    'Netrokona' => 'Netrokona',
                                                    'Nilphamari' => 'Nilphamari',
                                                    'Noakhali' => 'Noakhali',
                                                    'Pabna' => 'Pabna',
                                                    'Panchagarh' => 'Panchagarh',
                                                    'Patuakhali' => 'Patuakhali',
                                                    'Pirojpur' => 'Pirojpur',
                                                    'Rajbari' => 'Rajbari',
                                                    'Rajshahi' => 'Rajshahi',
                                                    'Rangamati' => 'Rangamati',
                                                    'Rangpur' => 'Rangpur',
                                                    'Satkhira' => 'Satkhira',
                                                    'Shariatpur' => 'Shariatpur',
                                                    'Sherpur' => 'Sherpur',
                                                    'Sirajganj' => 'Sirajganj',
                                                    'Sunamganj' => 'Sunamganj',
                                                    'Sylhet' => 'Sylhet',
                                                    'Tangail' => 'Tangail',
                                                    'Thakurgaon' => 'Thakurgaon',
                                                ])
                                                ->searchable()
                                                ->native(false)
                                                ->placeholder('Select district'),
                                        ])->compact(),

                                    // Right: Branding
                                    Section::make('Logo & Branding')
                                        ->schema([
                                            FileUpload::make('logo')
                                                ->label('Client Logo')
                                                ->image()
                                                ->disk(self::getStorageDisk())
                                                ->directory('clients/logos')
                                                ->imageEditor()
                                                ->imagePreviewHeight('100')
                                                ->downloadable()
                                                ->afterStateUpdated(function ($state, callable $set) {
                                                    if ($state) {
                                                        $set('logo_disk', self::getStorageDisk());
                                                    }
                                                }),
                                            Hidden::make('logo_disk')
                                                ->default(self::getStorageDisk()),
                                            TextInput::make('sort_order')
                                                ->label('Sort Order')
                                                ->numeric()
                                                ->default(0)
                                                ->helperText('Lower = first'),
                                        ])->compact(),
                                ]),
                        ]),

                    // ━━ TAB 2: Contact & Principal ━━
                    Tab::make('Contact')
                        ->icon('heroicon-o-phone')
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 2])
                                ->schema([
                                    Section::make('Contact Details')
                                        ->schema([
                                            TextInput::make('email')
                                                ->label('Email')
                                                ->email()
                                                ->maxLength(255)
                                                ->prefixIcon('heroicon-o-envelope'),
                                            TextInput::make('phone')
                                                ->label('Phone')
                                                ->tel()
                                                ->maxLength(20)
                                                ->prefixIcon('heroicon-o-phone'),
                                            TextInput::make('website')
                                                ->label('Website')
                                                ->url()
                                                ->prefix('https://')
                                                ->maxLength(255),
                                            Textarea::make('address')
                                                ->label('Full Address')
                                                ->rows(2)
                                                ->maxLength(500)
                                                ->columnSpanFull(),
                                        ])->compact(),

                                    Section::make('Principal / Head')
                                        ->schema([
                                            TextInput::make('principal_name')
                                                ->label('Name')
                                                ->maxLength(255)
                                                ->prefixIcon('heroicon-o-user'),
                                            TextInput::make('principal_phone')
                                                ->label('Phone')
                                                ->tel()
                                                ->maxLength(20)
                                                ->prefixIcon('heroicon-o-phone'),
                                        ])->compact(),
                                ]),
                        ]),

                    // ━━ TAB 3: Teams & Modules ━━
                    Tab::make('Teams & Modules')
                        ->icon('heroicon-o-squares-2x2')
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 2])
                                ->schema([
                                    Section::make('Assigned Teams')
                                        ->description('Which teams manage this client')
                                        ->schema([
                                            Select::make('teams')
                                                ->label('')
                                                ->relationship('teams', 'name')
                                                ->multiple()
                                                ->searchable()
                                                ->preload()
                                                ->placeholder('Select teams'),
                                        ])->compact(),

                                    Section::make('ERP Modules')
                                        ->description('Modules this client uses')
                                        ->schema([
                                            CheckboxList::make('erpModules')
                                                ->label('')
                                                ->relationship('erpModules', 'name')
                                                ->bulkToggleable()
                                                ->searchable()
                                                ->columns(2),
                                        ])->compact(),
                                ]),
                        ]),

                    // ━━ TAB 4: Contract & Status ━━
                    Tab::make('Contract & Status')
                        ->icon('heroicon-o-calendar-days')
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 2])
                                ->schema([
                                    Section::make('Contract Period')
                                        ->schema([
                                            DatePicker::make('contract_start')
                                                ->label('Start Date')
                                                ->native(false)
                                                ->prefixIcon('heroicon-o-calendar'),
                                            DatePicker::make('contract_end')
                                                ->label('End Date')
                                                ->native(false)
                                                ->after('contract_start')
                                                ->prefixIcon('heroicon-o-calendar'),
                                        ])->compact(),

                                    Section::make('Visibility & Status')
                                        ->schema([
                                            Toggle::make('is_active')
                                                ->label('Active')
                                                ->default(true)
                                                ->helperText('Is this client account active?'),
                                            Toggle::make('is_live')
                                                ->label('Live on Website')
                                                ->default(false)
                                                ->helperText('Show on public website'),
                                            Toggle::make('is_featured')
                                                ->label('Featured')
                                                ->default(false)
                                                ->helperText('Highlight on homepage'),
                                        ])->compact(),
                                ]),
                        ]),

                    // ━━ TAB 5: Domain & Hosting ━━
                    Tab::make('Domain & Hosting')
                        ->icon('heroicon-o-globe-alt')
                        ->schema([
                            Grid::make(['default' => 1, 'lg' => 2])
                                ->schema([
                                    Section::make('Domain Information')
                                        ->description('Website domain details')
                                        ->schema([
                                            TextInput::make('domain_name')
                                                ->label('Domain Name')
                                                ->placeholder('example.edu.bd')
                                                ->maxLength(255)
                                                ->prefixIcon('heroicon-o-globe-alt'),
                                            DatePicker::make('domain_expiry')
                                                ->label('Domain Expiry Date')
                                                ->native(false)
                                                ->prefixIcon('heroicon-o-calendar'),
                                            TextInput::make('domain_provider')
                                                ->label('Domain Provider')
                                                ->placeholder('e.g. GoDaddy, Namecheap, BTCL')
                                                ->maxLength(255)
                                                ->prefixIcon('heroicon-o-building-storefront'),
                                        ])->compact(),

                                    Section::make('Hosting Information')
                                        ->description('Server & hosting details')
                                        ->schema([
                                            TextInput::make('hosting_provider')
                                                ->label('Hosting Provider')
                                                ->placeholder('e.g. cPanel, DigitalOcean, AWS')
                                                ->maxLength(255)
                                                ->prefixIcon('heroicon-o-server-stack'),
                                            TextInput::make('hosting_package')
                                                ->label('Hosting Package / Plan')
                                                ->placeholder('e.g. Starter, Pro, Business')
                                                ->maxLength(255)
                                                ->prefixIcon('heroicon-o-cube'),
                                            DatePicker::make('hosting_expiry')
                                                ->label('Hosting Expiry Date')
                                                ->native(false)
                                                ->prefixIcon('heroicon-o-calendar'),
                                            Textarea::make('hosting_notes')
                                                ->label('Notes')
                                                ->placeholder('Server IP, login URL, etc.')
                                                ->rows(2)
                                                ->maxLength(1000),
                                        ])->compact(),
                                ]),
                        ]),

                ])->columnSpanFull()
                ->persistTabInQueryString(),
        ]);
    }
}
