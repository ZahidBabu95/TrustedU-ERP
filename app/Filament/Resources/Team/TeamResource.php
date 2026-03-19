<?php

namespace App\Filament\Resources\Team;

use App\Filament\Resources\Team\Pages\CreateTeamMember;
use App\Filament\Resources\Team\Pages\EditTeamMember;
use App\Filament\Resources\Team\Pages\ListTeamMembers;
use App\Filament\Resources\Team\Pages\ViewTeamMember;
use App\Models\Team;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Facades\Hash;

class TeamResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    protected static ?string $navigationLabel = 'Team Member';
    protected static ?string $modelLabel = 'Team Member';
    protected static ?string $pluralModelLabel = 'Team Members';
    protected static ?string $slug = 'team';

    public static function getNavigationGroup(): ?string { return 'Management'; }
    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Team Member')
                    ->tabs([
                        // ─── TAB 1: Basic Info ───
                        Tab::make('Basic Info')
                            ->icon('heroicon-o-user')
                            ->schema([
                                Section::make('Account Information')
                                    ->description('Basic login and contact details')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('name')
                                                ->required()
                                                ->maxLength(255)
                                                ->placeholder('Full Name'),
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
                                            TextInput::make('password')
                                                ->password()
                                                ->dehydrateStateUsing(fn(?string $state): ?string => $state ? Hash::make($state) : null)
                                                ->dehydrated(fn(?string $state): bool => filled($state))
                                                ->required(fn(string $operation): bool => $operation === 'create')
                                                ->placeholder('Min 8 characters'),
                                        ]),
                                    ]),
                                Section::make('Team Assignment')
                                    ->description('Assign this member to one or more teams')
                                    ->schema([
                                        Select::make('teams')
                                            ->label('Teams')
                                            ->relationship('teams', 'name')
                                            ->multiple()
                                            ->preload()
                                            ->searchable()
                                            ->helperText('Select the teams this member belongs to'),
                                    ]),
                            ]),

                        // ─── TAB 2: Professional Info ───
                        Tab::make('Professional')
                            ->icon('heroicon-o-briefcase')
                            ->schema([
                                Section::make('Professional Details')
                                    ->description('Work-related information')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('designation')
                                                ->placeholder('e.g. Software Engineer'),
                                            TextInput::make('department')
                                                ->placeholder('e.g. Engineering'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            DatePicker::make('profile.joining_date')
                                                ->label('Joining Date')
                                                ->native(false),
                                            Select::make('profile.employment_type')
                                                ->label('Employment Type')
                                                ->options([
                                                    'full_time' => 'Full Time',
                                                    'part_time' => 'Part Time',
                                                    'intern'    => 'Intern',
                                                    'contract'  => 'Contract',
                                                ])
                                                ->default('full_time'),
                                        ]),
                                    ]),

                                Section::make('Personal Details')
                                    ->description('Personal information')
                                    ->schema([
                                        FileUpload::make('profile.profile_photo')
                                            ->label('Profile Photo')
                                            ->image()
                                            ->avatar()
                                            ->directory('team-photos')
                                            ->maxSize(2048),
                                        Grid::make(['md' => 2])->schema([
                                            DatePicker::make('profile.date_of_birth')
                                                ->label('Date of Birth')
                                                ->native(false),
                                            Select::make('profile.gender')
                                                ->label('Gender')
                                                ->options([
                                                    'male'   => 'Male',
                                                    'female' => 'Female',
                                                    'other'  => 'Other',
                                                ]),
                                        ]),
                                        Textarea::make('profile.address')
                                            ->label('Address')
                                            ->rows(2)
                                            ->placeholder('Full address'),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('profile.emergency_contact_name')
                                                ->label('Emergency Contact Name'),
                                            TextInput::make('profile.emergency_contact_phone')
                                                ->label('Emergency Contact Phone')
                                                ->tel(),
                                        ]),
                                        Textarea::make('profile.bio')
                                            ->label('Bio / Notes')
                                            ->rows(3)
                                            ->placeholder('Brief bio or notes about this team member'),
                                    ]),
                            ]),

                        // ─── TAB 3: Documents ───
                        Tab::make('Documents')
                            ->icon('heroicon-o-document-text')
                            ->schema([
                                Section::make('Employee Documents')
                                    ->description('Upload CV, NID, certificates and other documents')
                                    ->schema([
                                        Repeater::make('documents')
                                            ->relationship()
                                            ->schema([
                                                Grid::make(['md' => 3])->schema([
                                                    TextInput::make('title')
                                                        ->required()
                                                        ->placeholder('Document name'),
                                                    Select::make('type')
                                                        ->options([
                                                            'cv'          => 'CV / Resume',
                                                            'nid'         => 'NID / ID Card',
                                                            'certificate' => 'Certificate',
                                                            'contract'    => 'Contract',
                                                            'other'       => 'Other',
                                                        ])
                                                        ->default('other'),
                                                    DatePicker::make('expiry_date')
                                                        ->label('Expiry Date')
                                                        ->native(false),
                                                ]),
                                                FileUpload::make('file_path')
                                                    ->label('File')
                                                    ->required()
                                                    ->directory('team-documents')
                                                    ->acceptedFileTypes(['application/pdf', 'image/*'])
                                                    ->maxSize(10240),
                                                Textarea::make('notes')
                                                    ->rows(1)
                                                    ->placeholder('Optional notes'),
                                            ])
                                            ->defaultItems(0)
                                            ->addActionLabel('Add Document')
                                            ->collapsible()
                                            ->itemLabel(fn(array $state): ?string => $state['title'] ?? 'New Document'),
                                    ]),
                            ]),

                        // ─── TAB 4: Financial Info ───
                        Tab::make('Financial')
                            ->icon('heroicon-o-banknotes')
                            ->schema([
                                Section::make('Bank Account')
                                    ->description('Bank account details for salary transfer')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('financial.bank_name')
                                                ->label('Bank Name')
                                                ->placeholder('e.g. Dutch Bangla Bank'),
                                            TextInput::make('financial.branch_name')
                                                ->label('Branch Name')
                                                ->placeholder('e.g. Gulshan Branch'),
                                        ]),
                                        Grid::make(['md' => 2])->schema([
                                            TextInput::make('financial.account_holder_name')
                                                ->label('Account Holder Name'),
                                            TextInput::make('financial.account_number')
                                                ->label('Account Number'),
                                        ]),
                                        TextInput::make('financial.routing_number')
                                            ->label('Routing Number')
                                            ->placeholder('Optional'),
                                    ]),

                                Section::make('Mobile Banking')
                                    ->description('Mobile banking info for quick payments')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            Select::make('financial.mobile_banking_type')
                                                ->label('Provider')
                                                ->options([
                                                    'bkash'  => 'bKash',
                                                    'nagad'  => 'Nagad',
                                                    'rocket' => 'Rocket',
                                                    'upay'   => 'Upay',
                                                    'other'  => 'Other',
                                                ]),
                                            TextInput::make('financial.mobile_banking_number')
                                                ->label('Mobile Number')
                                                ->tel()
                                                ->placeholder('01XXX-XXXXXX'),
                                        ]),
                                    ]),

                                Section::make('Salary')
                                    ->description('Salary information (admin only)')
                                    ->schema([
                                        TextInput::make('financial.base_salary')
                                            ->label('Base Salary (BDT)')
                                            ->numeric()
                                            ->prefix('৳')
                                            ->placeholder('0.00'),
                                    ]),
                            ]),

                        // ─── TAB 5: System Access ───
                        Tab::make('System Access')
                            ->icon('heroicon-o-shield-check')
                            ->schema([
                                Section::make('Role & Permissions')
                                    ->description('Control panel access and permissions')
                                    ->schema([
                                        Grid::make(['md' => 2])->schema([
                                            Select::make('role')
                                                ->options([
                                                    'super_admin' => 'Super Admin',
                                                    'admin'       => 'Admin',
                                                    'editor'      => 'Editor',
                                                    'sales'       => 'Sales',
                                                    'team_member' => 'Team Member',
                                                    'viewer'      => 'Viewer',
                                                ])
                                                ->default('team_member')
                                                ->required(),
                                            Toggle::make('is_active')
                                                ->label('Active')
                                                ->default(true)
                                                ->helperText('Inactive users cannot login'),
                                        ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile.profile_photo')
                    ->label('')
                    ->circular()
                    ->size(36)
                    ->defaultImageUrl(fn(User $record): string =>
                        'https://ui-avatars.com/api/?name=' . urlencode($record->name) . '&background=6c5ce7&color=fff&size=80'
                    ),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn(User $record): string => $record->designation ?? ''),
                TextColumn::make('email')
                    ->searchable()
                    ->color('gray')
                    ->size('sm'),
                TextColumn::make('department')
                    ->searchable()
                    ->badge()
                    ->color('gray'),
                TextColumn::make('teams.name')
                    ->label('Teams')
                    ->badge()
                    ->color('info')
                    ->separator(', '),
                TextColumn::make('role')
                    ->badge()
                    ->formatStateUsing(fn(string $state): string => str_replace('_', ' ', ucwords($state, '_'))),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('created_at')
                    ->label('Joined')
                    ->date('M d, Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->filters([
                SelectFilter::make('role')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'admin'       => 'Admin',
                        'editor'      => 'Editor',
                        'sales'       => 'Sales',
                        'team_member' => 'Team Member',
                        'viewer'      => 'Viewer',
                    ]),
                SelectFilter::make('is_active')
                    ->options([
                        '1' => 'Active',
                        '0' => 'Inactive',
                    ])
                    ->label('Status'),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
                RestoreAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeamMembers::route('/'),
            'create' => CreateTeamMember::route('/create'),
            'view'   => ViewTeamMember::route('/{record}'),
            'edit'   => EditTeamMember::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([SoftDeletingScope::class]);
    }
}
