<?php

namespace App\Filament\Resources\TeamGroups;

use App\Filament\Resources\TeamGroups\Pages\CreateTeamGroup;
use App\Filament\Resources\TeamGroups\Pages\EditTeamGroup;
use App\Filament\Resources\TeamGroups\Pages\ListTeamGroups;
use App\Models\Team;
use App\Models\User;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamGroupResource extends Resource
{
    protected static ?string $model = Team::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleGroup;

    protected static ?string $navigationLabel = 'Teams';
    protected static ?string $modelLabel = 'Team';
    protected static ?string $pluralModelLabel = 'Teams';
    protected static ?string $slug = 'team-groups';

    public static function getNavigationGroup(): ?string { return 'Management'; }
    protected static ?int $navigationSort = 2;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Team Information')
                    ->description('Create and manage teams')
                    ->schema([
                        Grid::make(['md' => 2])->schema([
                            TextInput::make('name')
                                ->required()
                                ->maxLength(255)
                                ->placeholder('e.g. Sales Team')
                                ->live(onBlur: true)
                                ->afterStateUpdated(fn ($state, callable $set) =>
                                    $set('slug', \Illuminate\Support\Str::slug($state))
                                ),
                            TextInput::make('slug')
                                ->required()
                                ->unique(ignoreRecord: true)
                                ->placeholder('auto-generated'),
                        ]),
                        Textarea::make('description')
                            ->rows(2)
                            ->placeholder('Brief description of this team'),
                        Grid::make(['md' => 3])->schema([
                            FileUpload::make('logo')
                                ->image()
                                ->directory('team-logos')
                                ->maxSize(1024),
                            ColorPicker::make('color')
                                ->label('Brand Color')
                                ->default('#6366f1'),
                            Toggle::make('is_active')
                                ->label('Active')
                                ->default(true),
                        ]),
                    ]),

                Section::make('Team Members')
                    ->description('Select users to add to this team')
                    ->schema([
                        Select::make('members')
                            ->label('Members')
                            ->relationship('members', 'name')
                            ->multiple()
                            ->preload()
                            ->searchable(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label('')
                    ->copyable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('members_count')
                    ->label('Members')
                    ->counts('members')
                    ->badge()
                    ->color('info'),
                TextColumn::make('description')
                    ->limit(50)
                    ->color('gray'),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->date('M d, Y')
                    ->sortable()
                    ->color('gray'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListTeamGroups::route('/'),
            'create' => CreateTeamGroup::route('/create'),
            'edit'   => EditTeamGroup::route('/{record}/edit'),
        ];
    }
}
