<?php

namespace App\Filament\Pages;

use App\Models\EmployeeFinancial;
use App\Models\EmployeeProfile;
use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    protected string $view = 'filament.pages.edit-profile';

    // Use index layout (with sidebar) instead of simple centered
    public function getLayout(): string
    {
        return 'filament-panels::components.layout.index';
    }

    public function getMaxWidth(): ?string
    {
        return 'full';
    }

    /**
     * Override mount to load profile + financial data alongside user data.
     */
    protected function afterFill(): void
    {
        $user = Auth::user();
        $profile = $user->profile ?? new EmployeeProfile();
        $financial = $user->financial ?? new EmployeeFinancial();

        $this->data = array_merge($this->data, [
            'phone'       => $user->phone,
            'department'  => $user->department,
            'designation' => $user->designation,

            // Profile
            'profile_photo'           => $profile->profile_photo,
            'date_of_birth'           => $profile->date_of_birth?->format('Y-m-d'),
            'gender'                  => $profile->gender,
            'address'                 => $profile->address,
            'bio'                     => $profile->bio,
            'joining_date'            => $profile->joining_date?->format('Y-m-d'),
            'employment_type'         => $profile->employment_type,
            'emergency_contact_name'  => $profile->emergency_contact_name,
            'emergency_contact_phone' => $profile->emergency_contact_phone,

            // Financial
            'bank_name'             => $financial->bank_name,
            'account_number'        => $financial->account_number,
            'account_holder_name'   => $financial->account_holder_name,
            'branch_name'           => $financial->branch_name,
            'routing_number'        => $financial->routing_number,
            'mobile_banking_type'   => $financial->mobile_banking_type,
            'mobile_banking_number' => $financial->mobile_banking_number,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ── Personal Information ──
                Section::make('Personal Information')
                    ->icon('heroicon-o-user')
                    ->description('Your basic profile information')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('profile_photo')
                            ->label('Profile Photo')
                            ->image()
                            ->avatar()
                            ->directory('team-photos')
                            ->maxSize(2048)
                            ->columnSpanFull(),
                        Grid::make(['md' => 2])->schema([
                            $this->getNameFormComponent(),
                            $this->getEmailFormComponent()
                                ->disabled()
                                ->dehydrated(false)
                                ->helperText('Email cannot be changed'),
                        ]),
                        Grid::make(['md' => 3])->schema([
                            TextInput::make('phone')
                                ->tel()
                                ->placeholder('+880 1XXX-XXXXXX'),
                            DatePicker::make('date_of_birth')
                                ->label('Date of Birth')
                                ->native(false)
                                ->displayFormat('d M Y'),
                            Select::make('gender')
                                ->options([
                                    'male'   => 'Male',
                                    'female' => 'Female',
                                    'other'  => 'Other',
                                ]),
                        ]),
                        Textarea::make('address')
                            ->rows(2)
                            ->placeholder('Your full address'),
                        Textarea::make('bio')
                            ->label('Bio / About Me')
                            ->rows(3)
                            ->placeholder('Tell us about yourself...'),
                    ]),

                // ── Professional Information ──
                Section::make('Professional Information')
                    ->icon('heroicon-o-briefcase')
                    ->description('Your work details (read-only)')
                    ->collapsible()
                    ->schema([
                        Grid::make(['md' => 2])->schema([
                            TextInput::make('designation')
                                ->disabled()
                                ->helperText('Contact admin to change'),
                            TextInput::make('department')
                                ->disabled()
                                ->helperText('Contact admin to change'),
                        ]),
                        Grid::make(['md' => 2])->schema([
                            DatePicker::make('joining_date')
                                ->label('Joining Date')
                                ->disabled()
                                ->native(false)
                                ->displayFormat('d M Y'),
                            Select::make('employment_type')
                                ->label('Employment Type')
                                ->disabled()
                                ->options([
                                    'full_time' => 'Full Time',
                                    'part_time' => 'Part Time',
                                    'contract'  => 'Contract',
                                    'intern'    => 'Intern',
                                    'freelance' => 'Freelance',
                                ]),
                        ]),
                    ]),

                // ── Emergency Contact ──
                Section::make('Emergency Contact')
                    ->icon('heroicon-o-phone')
                    ->description('Who to contact in case of emergency')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(['md' => 2])->schema([
                            TextInput::make('emergency_contact_name')
                                ->label('Contact Name')
                                ->placeholder('Emergency contact name'),
                            TextInput::make('emergency_contact_phone')
                                ->label('Contact Phone')
                                ->tel()
                                ->placeholder('Phone number'),
                        ]),
                    ]),

                // ── Financial Information ──
                Section::make('Financial Information')
                    ->icon('heroicon-o-banknotes')
                    ->description('Your bank and mobile banking details')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Grid::make(['md' => 2])->schema([
                            TextInput::make('bank_name')
                                ->placeholder('e.g. Dutch Bangla Bank'),
                            TextInput::make('branch_name')
                                ->label('Branch')
                                ->placeholder('Branch name'),
                        ]),
                        Grid::make(['md' => 2])->schema([
                            TextInput::make('account_holder_name')
                                ->placeholder('As per bank records'),
                            TextInput::make('account_number')
                                ->placeholder('Bank account number'),
                        ]),
                        TextInput::make('routing_number')
                            ->placeholder('Optional'),
                        Grid::make(['md' => 2])->schema([
                            Select::make('mobile_banking_type')
                                ->label('Mobile Banking')
                                ->options([
                                    'bkash'  => 'bKash',
                                    'nagad'  => 'Nagad',
                                    'rocket' => 'Rocket',
                                    'upay'   => 'Upay',
                                    'other'  => 'Other',
                                ]),
                            TextInput::make('mobile_banking_number')
                                ->label('Mobile Banking Number')
                                ->tel()
                                ->placeholder('01XXXXXXXXX'),
                        ]),
                    ]),

                // ── Security & Password ──
                Section::make('Security & Password')
                    ->icon('heroicon-o-shield-check')
                    ->description('Update your account password')
                    ->collapsible()
                    ->collapsed()
                    ->schema([
                        Toggle::make('change_password')
                            ->label('I want to change my password')
                            ->live()
                            ->dehydrated(false),

                        Group::make([
                            TextInput::make('currentPassword')
                                ->label('Current Password')
                                ->password()
                                ->revealable()
                                ->required()
                                ->currentPassword()
                                ->columnSpanFull(),
                            Grid::make(['md' => 2])->schema([
                                TextInput::make('password')
                                    ->label('New Password')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->rule(Password::default())
                                    ->autocomplete('new-password')
                                    ->dehydrated(fn ($state): bool => filled($state))
                                    ->dehydrateStateUsing(fn ($state): string => Hash::make($state))
                                    ->live(debounce: 500)
                                    ->same('passwordConfirmation'),
                                TextInput::make('passwordConfirmation')
                                    ->label('Confirm New Password')
                                    ->password()
                                    ->revealable()
                                    ->required()
                                    ->dehydrated(false),
                            ]),
                        ])->visible(fn ($get): bool => (bool) $get('change_password')),
                    ]),
            ]);
    }

    /**
     * Handle saving profile + financial data alongside user data.
     */
    protected function afterSave(): void
    {
        $data = $this->form->getState();
        $user = Auth::user();

        // Update phone
        $user->update(['phone' => $data['phone'] ?? $user->phone]);

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
                'routing_number'        => $data['routing_number'] ?? null,
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
