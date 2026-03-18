<?php

namespace App\Filament\Pages;

use Filament\Auth\Pages\EditProfile as BaseEditProfile;
use Filament\Schemas\Schema;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Group;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class EditProfile extends BaseEditProfile
{
    // Force sidebar/index layout instead of the default centered simple layout
    public static function isSimple(): bool
    {
        return false;
    }

    public function getMaxWidth(): ?string
    {
        return '4xl';
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Profile Information')
                    ->description('Update your account\'s profile information and avatar.')
                    ->icon('heroicon-o-user')
                    ->schema([
                        FileUpload::make('avatar')
                            ->label('Profile Picture')
                            ->avatar()
                            ->image()
                            ->directory('avatars')
                            ->maxSize(2048)
                            ->columnSpanFull(),

                        $this->getNameFormComponent(),

                        $this->getEmailFormComponent()
                            ->disabled()
                            ->dehydrated(false)
                            ->helperText('Email address cannot be changed.'),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->maxLength(20),
                    ])->columns([
                        'sm' => 1,
                        'md' => 2,
                    ]),

                Section::make('Security & Password')
                    ->description('Change your account password. You must provide your current password first.')
                    ->icon('heroicon-o-shield-check')
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
                        ])->visible(fn ($get): bool => (bool) $get('change_password'))
                          ->columns([
                              'sm' => 1,
                              'md' => 2,
                          ]),
                    ]),
            ]);
    }
}
