<?php

namespace App\Filament\Resources\Users;

use App\Filament\Resources\Users\Pages\CreateUser;
use App\Filament\Resources\Users\Pages\EditUser;
use App\Filament\Resources\Users\Pages\ListUsers;
use App\Filament\Resources\Users\Pages\ViewUser;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-users';
    public static function getNavigationGroup(): ?string { return 'Platform'; }
    protected static ?int $navigationSort = 21;
    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('is_active', true)->count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('User')
                ->tabs([
                    // ── Tab 1: Basic Info ──
                    Tab::make('Basic Info')
                        ->icon('heroicon-o-user')
                        ->schema([
                            Section::make('Account Details')
                                ->description('User account information')
                                ->schema([
                                    Grid::make(['md' => 2])->schema([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder('Full name'),
                                        TextInput::make('email')
                                            ->email()
                                            ->required()
                                            ->unique(ignoreRecord: true)
                                            ->placeholder('email@example.com'),
                                    ]),
                                    Grid::make(['md' => 2])->schema([
                                        TextInput::make('phone')
                                            ->tel()
                                            ->placeholder('+880 1XXX-XXXXXX'),
                                        Select::make('role')
                                            ->options([
                                                'super_admin'  => '👑 Super Admin',
                                                'admin'        => '🛡️ Admin',
                                                'editor'       => '✏️ Editor',
                                                'sales'        => '💼 Sales',
                                                'team_member'  => '👤 Team Member',
                                            ])
                                            ->default('team_member')
                                            ->required(),
                                    ]),
                                    Grid::make(['md' => 2])->schema([
                                        TextInput::make('department')
                                            ->placeholder('Engineering, Marketing, etc.'),
                                        TextInput::make('designation')
                                            ->placeholder('Manager, Developer, etc.'),
                                    ]),
                                ]),

                            Section::make('Security')
                                ->description('Password and account status')
                                ->schema([
                                    Grid::make(['md' => 2])->schema([
                                        TextInput::make('password')
                                            ->password()
                                            ->revealable()
                                            ->dehydrateStateUsing(fn ($state) => filled($state) ? Hash::make($state) : null)
                                            ->dehydrated(fn ($state) => filled($state))
                                            ->required(fn (string $operation) => $operation === 'create')
                                            ->rule(Password::defaults())
                                            ->placeholder('Leave blank to keep current'),
                                        DateTimePicker::make('email_verified_at')
                                            ->label('Email Verified'),
                                    ]),
                                    Grid::make(['md' => 2])->schema([
                                        Toggle::make('is_active')
                                            ->label('Active Account')
                                            ->default(true)
                                            ->helperText('Inactive users cannot login'),
                                    ]),
                                ]),
                        ]),

                    // ── Tab 2: Permissions ──
                    Tab::make('Permissions')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Section::make('Feature Access Control')
                                ->description('Select which features this user can access. Super Admin and Admin roles have all permissions by default.')
                                ->schema(
                                    collect(User::PERMISSION_GROUPS)->map(function ($perms, $group) {
                                        return CheckboxList::make('permissions')
                                            ->label($group)
                                            ->options(
                                                collect($perms)->mapWithKeys(fn ($p) => [$p => User::PERMISSIONS[$p] ?? $p])->toArray()
                                            )
                                            ->bulkToggleable()
                                            ->columns(['default' => 2])
                                            ->gridDirection('row');
                                    })->values()->toArray()
                                ),
                        ]),

                    // ── Tab 3: Teams ──
                    Tab::make('Teams')
                        ->icon('heroicon-o-user-group')
                        ->schema([
                            Section::make('Team Assignments')
                                ->description('Assign user to teams')
                                ->schema([
                                    CheckboxList::make('teams')
                                        ->relationship('teams', 'name')
                                        ->columns(['default' => 2])
                                        ->bulkToggleable()
                                        ->label('Assigned Teams'),
                                ]),
                        ]),
                ])
                ->columnSpanFull(),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make('User Information')
                ->schema([
                    Grid::make(['md' => 3])->schema([
                        TextEntry::make('name')->weight('bold'),
                        TextEntry::make('email'),
                        TextEntry::make('phone')->placeholder('—'),
                    ]),
                    Grid::make(['md' => 3])->schema([
                        TextEntry::make('role')
                            ->badge()
                            ->color(fn (string $state) => match ($state) {
                                'super_admin' => 'danger',
                                'admin'       => 'warning',
                                'editor'      => 'info',
                                'sales'       => 'success',
                                default       => 'gray',
                            }),
                        TextEntry::make('department')->placeholder('—'),
                        TextEntry::make('designation')->placeholder('—'),
                    ]),
                    Grid::make(['md' => 3])->schema([
                        IconEntry::make('is_active')->boolean()->label('Active'),
                        TextEntry::make('last_login_at')->dateTime()->placeholder('Never'),
                        TextEntry::make('created_at')->dateTime(),
                    ]),
                ]),

            Section::make('Permissions')
                ->schema([
                    TextEntry::make('permissions')
                        ->label('Granted Permissions')
                        ->badge()
                        ->separator(',')
                        ->formatStateUsing(fn ($state) => User::PERMISSIONS[$state] ?? $state)
                        ->color('success')
                        ->placeholder('Using role defaults'),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->email),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => ucwords(str_replace('_', ' ', $state)))
                    ->color(fn (string $state) => match ($state) {
                        'super_admin' => 'danger',
                        'admin'       => 'warning',
                        'editor'      => 'info',
                        'sales'       => 'success',
                        default       => 'gray',
                    }),
                TextColumn::make('department')
                    ->placeholder('—')
                    ->size('sm')
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('designation')
                    ->placeholder('—')
                    ->size('sm')
                    ->color('gray')
                    ->toggleable(),
                TextColumn::make('teams_count')
                    ->label('Teams')
                    ->counts('teams')
                    ->badge()
                    ->color('gray'),
                TextColumn::make('permissions')
                    ->label('Perms')
                    ->formatStateUsing(fn ($record) => count($record->permissions ?? []))
                    ->badge()
                    ->color(fn ($record) => count($record->permissions ?? []) > 0 ? 'info' : 'gray')
                    ->suffix(fn ($record) => count($record->permissions ?? []) > 0 ? '' : ' (role)')
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->label('Active'),
                TextColumn::make('last_login_at')
                    ->label('Last Login')
                    ->since()
                    ->placeholder('Never')
                    ->sortable()
                    ->color('gray')
                    ->size('sm'),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->sortable()
                    ->color('gray')
                    ->size('sm')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin'       => 'Admin',
                        'editor'      => 'Editor',
                        'sales'       => 'Sales',
                        'team_member' => 'Team Member',
                    ])
                    ->multiple(),
                TernaryFilter::make('is_active')
                    ->label('Active Status'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                // Login As — Impersonate (direct link, avoids Livewire session issues)
                Action::make('impersonate')
                    ->label('Login As')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->color('warning')
                    ->visible(fn ($record) =>
                        Auth::user()?->isSuperAdmin()
                        && $record->id !== Auth::id()
                        && !$record->isSuperAdmin()
                    )
                    ->url(fn ($record) => route('impersonation.start', $record)),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn () => Auth::user()?->isSuperAdmin()),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->poll('60s');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUsers::route('/'),
            'create' => CreateUser::route('/create'),
            'view'   => ViewUser::route('/{record}'),
            'edit'   => EditUser::route('/{record}/edit'),
        ];
    }
}
