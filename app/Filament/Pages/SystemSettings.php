<?php

namespace App\Filament\Pages;

use App\Models\SystemSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;

class SystemSettings extends Page
{
    protected string $view = 'filament.pages.system-settings';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cog-6-tooth';
    protected static ?string $navigationLabel = 'System Settings';
    protected static ?string $title = 'System Settings';
    protected static ?string $slug = 'system-settings';

    public static function getNavigationGroup(): ?string { return 'Platform'; }
    protected static ?int $navigationSort = 3;

    public array $data = [];

    public function mount(): void
    {
        // Seed defaults if empty
        if (SystemSetting::count() === 0) {
            SystemSetting::seedDefaults();
        }

        // Load all settings into form data
        $settings = SystemSetting::all();
        foreach ($settings as $setting) {
            $value = $setting->value;
            if ($setting->is_encrypted && $value) {
                try {
                    $value = \Illuminate\Support\Facades\Crypt::decryptString($value);
                } catch (\Exception $e) {
                    $value = '';
                }
            }
            $this->data[$setting->key] = $value;
        }
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        // ─── GENERAL ───
                        Tab::make('General')
                            ->icon('heroicon-o-building-office')
                            ->schema([
                                Section::make('Application')
                                    ->description('Basic application identity settings')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('app_name')
                                                ->label('Application Name')
                                                ->required()
                                                ->placeholder('TrustedU ERP'),
                                            TextInput::make('company_name')
                                                ->label('Company Name')
                                                ->placeholder('TrustedU'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            FileUpload::make('company_logo')
                                                ->label('Company Logo')
                                                ->image()
                                                ->directory('system')
                                                ->maxSize(2048),
                                            FileUpload::make('favicon')
                                                ->label('Favicon')
                                                ->image()
                                                ->directory('system')
                                                ->maxSize(512),
                                        ]),
                                    ]),
                                Section::make('Regional')
                                    ->description('Locale and currency settings')
                                    ->schema([
                                        Grid::make(['md' => 3])->schema([
                                            Select::make('timezone')
                                                ->label('Timezone')
                                                ->options([
                                                    'Asia/Dhaka'     => 'Asia/Dhaka (BST)',
                                                    'UTC'            => 'UTC',
                                                    'America/New_York' => 'America/New_York (EST)',
                                                    'Europe/London'  => 'Europe/London (GMT)',
                                                    'Asia/Kolkata'   => 'Asia/Kolkata (IST)',
                                                ])
                                                ->searchable(),
                                            Select::make('currency')
                                                ->label('Currency')
                                                ->options([
                                                    'BDT' => '৳ BDT (Taka)',
                                                    'USD' => '$ USD (Dollar)',
                                                    'EUR' => '€ EUR (Euro)',
                                                    'GBP' => '£ GBP (Pound)',
                                                    'INR' => '₹ INR (Rupee)',
                                                ]),
                                            Select::make('date_format')
                                                ->label('Date Format')
                                                ->options([
                                                    'd M, Y' => 'd M, Y (18 Mar, 2026)',
                                                    'Y-m-d'  => 'Y-m-d (2026-03-18)',
                                                    'm/d/Y'  => 'm/d/Y (03/18/2026)',
                                                    'd/m/Y'  => 'd/m/Y (18/03/2026)',
                                                ]),
                                        ]),
                                    ]),
                            ]),

                        // ─── SMS ───
                        Tab::make('SMS / Messaging')
                            ->icon('heroicon-o-chat-bubble-left-right')
                            ->schema([
                                Section::make('SMS Configuration')
                                    ->description('Configure SMS gateway for notifications')
                                    ->schema([
                                        Toggle::make('sms_enabled')
                                            ->label('Enable SMS')
                                            ->helperText('Turn on/off SMS notifications'),
                                        Grid::make(['md' => 2])->schema([
                                            Select::make('sms_provider')
                                                ->label('SMS Provider')
                                                ->options([
                                                    'twilio'  => 'Twilio',
                                                    'bulksms' => 'Bulk SMS BD',
                                                    'custom'  => 'Custom API',
                                                ])
                                                ->placeholder('Select provider'),
                                            TextInput::make('sms_sender_id')
                                                ->label('Sender ID')
                                                ->placeholder('e.g. TrustedU'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('sms_api_key')
                                                ->label('API Key')
                                                ->password()
                                                ->revealable()
                                                ->placeholder('Enter API key'),
                                            TextInput::make('sms_api_secret')
                                                ->label('API Secret')
                                                ->password()
                                                ->revealable()
                                                ->placeholder('Enter API secret'),
                                        ]),
                                    ]),
                            ]),

                        // ─── EMAIL ───
                        Tab::make('Email')
                            ->icon('heroicon-o-envelope')
                            ->schema([
                                Section::make('Mail Configuration')
                                    ->description('SMTP / Mail service configuration')
                                    ->schema([
                                        Grid::make(['md' => 3])->schema([
                                            Select::make('mail_driver')
                                                ->label('Mail Driver')
                                                ->options([
                                                    'smtp'     => 'SMTP',
                                                    'sendmail' => 'Sendmail',
                                                    'mailgun'  => 'Mailgun',
                                                    'ses'      => 'Amazon SES',
                                                    'postmark' => 'Postmark',
                                                ]),
                                            TextInput::make('mail_host')
                                                ->label('Mail Host')
                                                ->placeholder('smtp.gmail.com'),
                                            TextInput::make('mail_port')
                                                ->label('Mail Port')
                                                ->numeric()
                                                ->placeholder('587'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('mail_username')
                                                ->label('Username')
                                                ->placeholder('your@email.com'),
                                            TextInput::make('mail_password')
                                                ->label('Password')
                                                ->password()
                                                ->revealable(),
                                        ]),
                                        Grid::make(['md' => 3])->schema([
                                            Select::make('mail_encryption')
                                                ->label('Encryption')
                                                ->options([
                                                    'tls'  => 'TLS',
                                                    'ssl'  => 'SSL',
                                                    'none' => 'None',
                                                ]),
                                            TextInput::make('mail_from_address')
                                                ->label('From Address')
                                                ->email()
                                                ->placeholder('noreply@trustedu.com'),
                                            TextInput::make('mail_from_name')
                                                ->label('From Name')
                                                ->placeholder('TrustedU ERP'),
                                        ]),
                                    ]),
                            ]),

                        // ─── STORAGE ───
                        Tab::make('Storage')
                            ->icon('heroicon-o-cloud')
                            ->schema([
                                Section::make('File Storage Configuration')
                                    ->description('Configure cloud storage (S3, Cloudflare R2). Existing local files stay local — new uploads go to cloud.')
                                    ->schema([
                                        Select::make('storage_provider')
                                            ->label('Storage Provider')
                                            ->options([
                                                'local' => 'Local (Default)',
                                                's3'    => 'Amazon S3',
                                                'r2'    => 'Cloudflare R2',
                                            ])
                                            ->live()
                                            ->helperText('Select where new file uploads should be stored'),
                                        TextInput::make('storage_account_id')
                                            ->label('Account ID')
                                            ->placeholder('Cloudflare Account ID')
                                            ->visible(fn ($get) => $get('storage_provider') === 'r2'),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('storage_access_key')
                                                ->label('Access Key ID')
                                                ->password()
                                                ->revealable()
                                                ->visible(fn ($get) => in_array($get('storage_provider'), ['s3', 'r2'])),
                                            TextInput::make('storage_secret_key')
                                                ->label('Secret Access Key')
                                                ->password()
                                                ->revealable()
                                                ->visible(fn ($get) => in_array($get('storage_provider'), ['s3', 'r2'])),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('storage_bucket')
                                                ->label('Bucket Name')
                                                ->placeholder('e.g. sl-checkout-invoice')
                                                ->visible(fn ($get) => in_array($get('storage_provider'), ['s3', 'r2'])),
                                            TextInput::make('storage_region')
                                                ->label('Region')
                                                ->placeholder('auto')
                                                ->visible(fn ($get) => in_array($get('storage_provider'), ['s3', 'r2'])),
                                        ]),
                                        TextInput::make('storage_endpoint')
                                            ->label('S3 API Endpoint')
                                            ->placeholder('https://<account_id>.r2.cloudflarestorage.com')
                                            ->helperText('Auto-generated from Account ID for R2, or custom for S3')
                                            ->visible(fn ($get) => in_array($get('storage_provider'), ['s3', 'r2'])),
                                        TextInput::make('storage_public_url')
                                            ->label('Public URL (CDN)')
                                            ->placeholder('https://pub-xxx.r2.dev')
                                            ->helperText('Public development URL for serving files via CDN')
                                            ->visible(fn ($get) => in_array($get('storage_provider'), ['s3', 'r2'])),
                                    ]),
                            ]),

                        // ─── PAYMENT ───
                        Tab::make('Payment')
                            ->icon('heroicon-o-credit-card')
                            ->schema([
                                Section::make('Payment Gateway')
                                    ->description('Configure payment processing (future-ready)')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            Select::make('payment_gateway')
                                                ->label('Payment Gateway')
                                                ->options([
                                                    'stripe'     => 'Stripe',
                                                    'sslcommerz' => 'SSLCommerz',
                                                    'paypal'     => 'PayPal',
                                                    'razorpay'   => 'Razorpay',
                                                ])
                                                ->placeholder('Select gateway'),
                                            Toggle::make('payment_sandbox')
                                                ->label('Sandbox Mode')
                                                ->helperText('Enable test/sandbox mode'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('payment_api_key')
                                                ->label('API Key')
                                                ->password()
                                                ->revealable(),
                                            TextInput::make('payment_api_secret')
                                                ->label('API Secret')
                                                ->password()
                                                ->revealable(),
                                        ]),
                                    ]),
                            ]),

                        // ─── SYSTEM ───
                        Tab::make('System')
                            ->icon('heroicon-o-wrench-screwdriver')
                            ->schema([
                                Section::make('System Controls')
                                    ->description('Core system settings')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            Toggle::make('maintenance_mode')
                                                ->label('Maintenance Mode')
                                                ->helperText('⚠️ Users will see a maintenance page'),
                                            Toggle::make('registration_enabled')
                                                ->label('User Registration')
                                                ->helperText('Allow new users to register'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            Select::make('default_user_role')
                                                ->label('Default User Role')
                                                ->options([
                                                    'super_admin' => 'Super Admin',
                                                    'admin'       => 'Admin',
                                                    'editor'      => 'Editor',
                                                    'sales'       => 'Sales',
                                                    'team_member' => 'Team Member',
                                                    'viewer'      => 'Viewer',
                                                ]),
                                            TextInput::make('items_per_page')
                                                ->label('Items Per Page')
                                                ->numeric()
                                                ->placeholder('25'),
                                        ]),
                                        Toggle::make('enable_activity_log')
                                            ->label('Activity Log')
                                            ->helperText('Log all user activities'),
                                    ]),
                            ]),

                        // ─── SECURITY ───
                        Tab::make('Security')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('Password Policy')
                                    ->description('Set password requirements for users')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('password_min_length')
                                                ->label('Min Password Length')
                                                ->numeric()
                                                ->minValue(6)
                                                ->maxValue(32),
                                            TextInput::make('max_login_attempts')
                                                ->label('Max Login Attempts')
                                                ->numeric()
                                                ->minValue(3)
                                                ->maxValue(20),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            Toggle::make('password_require_uppercase')
                                                ->label('Require Uppercase'),
                                            Toggle::make('password_require_numbers')
                                                ->label('Require Numbers'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            Toggle::make('password_require_symbols')
                                                ->label('Require Symbols'),
                                            Toggle::make('two_factor_enabled')
                                                ->label('Two-Factor Auth (2FA)')
                                                ->helperText('Enable 2FA for all users'),
                                        ]),
                                    ]),
                                Section::make('Session & SSL')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('session_timeout')
                                                ->label('Session Timeout (minutes)')
                                                ->numeric()
                                                ->placeholder('120'),
                                            Toggle::make('force_ssl')
                                                ->label('Force HTTPS')
                                                ->helperText('Redirect all traffic to HTTPS'),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public function save(): void
    {
        $data = $this->data;

        foreach ($data as $key => $value) {
            $setting = SystemSetting::where('key', $key)->first();
            if ($setting) {
                // Handle toggle conversion
                if ($setting->type === 'toggle') {
                    $value = $value ? '1' : '0';
                }

                // Handle encryption
                $storeValue = $value;
                if ($setting->is_encrypted && $storeValue !== null && $storeValue !== '') {
                    $storeValue = \Illuminate\Support\Facades\Crypt::encryptString((string) $storeValue);
                }

                // Log change if value differs
                if ($setting->value !== $storeValue) {
                    SystemSetting::logChangePublic($key, $setting->value, $storeValue);
                }

                $setting->update(['value' => $storeValue]);
            }
        }

        SystemSetting::clearCache();

        Notification::make()
            ->title('Settings saved successfully!')
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }
}
