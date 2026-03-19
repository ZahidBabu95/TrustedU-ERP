<?php

namespace App\Filament\Pages;

use App\Models\EmployeeFinancial;
use App\Models\EmployeeProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Support\Facades\Auth;

class MyProfile extends Page
{
    protected string $view = 'filament.pages.my-profile';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'My Profile';
    protected static ?string $title = 'My Profile';
    protected static ?int $navigationSort = 99;

    protected static bool $shouldRegisterNavigation = false;

    public ?array $data = [];

    public function mount(): void
    {
        $user = Auth::user();
        $profile = $user->profile ?? new EmployeeProfile();
        $financial = $user->financial ?? new EmployeeFinancial();

        $this->form->fill([
            'name'  => $user->name,
            'email' => $user->email,
            'phone' => $user->phone,
            'department' => $user->department,
            'designation' => $user->designation,

            'profile_photo'           => $profile->profile_photo,
            'date_of_birth'           => $profile->date_of_birth?->format('Y-m-d'),
            'gender'                  => $profile->gender,
            'address'                 => $profile->address,
            'bio'                     => $profile->bio,
            'emergency_contact_name'  => $profile->emergency_contact_name,
            'emergency_contact_phone' => $profile->emergency_contact_phone,

            'bank_name'             => $financial->bank_name,
            'account_number'        => $financial->account_number,
            'account_holder_name'   => $financial->account_holder_name,
            'branch_name'           => $financial->branch_name,
            'mobile_banking_type'   => $financial->mobile_banking_type,
            'mobile_banking_number' => $financial->mobile_banking_number,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Profile')
                    ->tabs([
                        Tab::make('Personal Info')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make()
                                    ->schema([
                                        FileUpload::make('profile_photo')
                                            ->label('Profile Photo')
                                            ->image()
                                            ->avatar()
                                            ->directory('team-photos')
                                            ->maxSize(2048),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('name')->required(),
                                            TextInput::make('email')->email()->disabled(),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('phone')->tel(),
                                            DatePicker::make('date_of_birth')->label('Date of Birth')->native(false),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            Select::make('gender')
                                                ->options([
                                                    'male' => 'Male', 'female' => 'Female', 'other' => 'Other',
                                                ]),
                                            TextInput::make('designation')->disabled()->label('Designation'),
                                        ]),
                                        Textarea::make('address')->rows(2),
                                        Textarea::make('bio')->label('Bio / About Me')->rows(3),
                                    ]),

                                Section::make('Emergency Contact')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('emergency_contact_name')->label('Contact Name'),
                                            TextInput::make('emergency_contact_phone')->label('Contact Phone')->tel(),
                                        ]),
                                    ]),
                            ]),

                        Tab::make('Financial Info')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Bank Account')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('bank_name')->label('Bank Name'),
                                            TextInput::make('branch_name')->label('Branch'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('account_holder_name')->label('Account Holder'),
                                            TextInput::make('account_number')->label('Account Number'),
                                        ]),
                                    ]),

                                Section::make('Mobile Banking')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            Select::make('mobile_banking_type')
                                                ->label('Provider')
                                                ->options([
                                                    'bkash'  => 'bKash',
                                                    'nagad'  => 'Nagad',
                                                    'rocket' => 'Rocket',
                                                    'upay'   => 'Upay',
                                                    'other'  => 'Other',
                                                ]),
                                            TextInput::make('mobile_banking_number')
                                                ->label('Mobile Number')
                                                ->tel(),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull(),
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        // Update user basic info
        $user->update([
            'name'  => $data['name'],
            'phone' => $data['phone'],
        ]);

        // Update or create profile
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'profile_photo'           => $data['profile_photo'] ?? null,
                'date_of_birth'           => $data['date_of_birth'] ?? null,
                'gender'                  => $data['gender'] ?? null,
                'address'                 => $data['address'] ?? null,
                'bio'                     => $data['bio'] ?? null,
                'emergency_contact_name'  => $data['emergency_contact_name'] ?? null,
                'emergency_contact_phone' => $data['emergency_contact_phone'] ?? null,
            ]
        );

        // Update or create financial
        $user->financial()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'bank_name'             => $data['bank_name'] ?? null,
                'account_number'        => $data['account_number'] ?? null,
                'account_holder_name'   => $data['account_holder_name'] ?? null,
                'branch_name'           => $data['branch_name'] ?? null,
                'mobile_banking_type'   => $data['mobile_banking_type'] ?? null,
                'mobile_banking_number' => $data['mobile_banking_number'] ?? null,
            ]
        );

        Notification::make()
            ->title('Profile updated successfully!')
            ->success()
            ->send();
    }
}
