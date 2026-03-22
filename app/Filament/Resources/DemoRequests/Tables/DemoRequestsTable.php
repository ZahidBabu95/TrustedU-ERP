<?php

namespace App\Filament\Resources\DemoRequests\Tables;

use App\Models\DemoRequest;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DemoRequestsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('contact_name')->searchable()->weight('bold')
                    ->description(fn ($record) => $record->institution_name),
                TextColumn::make('institution_name')->searchable()->limit(35)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('institution_type')->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'school'     => 'info',
                        'college'    => 'primary',
                        'university' => 'success',
                        'madrasha'   => 'warning',
                        default      => 'gray',
                    }),
                TextColumn::make('phone')->searchable(),
                TextColumn::make('email')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('district'),
                TextColumn::make('status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'pending'   => 'warning',
                        'contacted' => 'info',
                        'demo_done' => 'primary',
                        'converted' => 'success',
                        'rejected'  => 'danger',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => match ($state) {
                        'pending'   => '🟡 Pending',
                        'contacted' => '📞 Contacted',
                        'demo_done' => '✅ Demo Done',
                        'converted' => '🚀 Converted',
                        'rejected'  => '❌ Rejected',
                        default     => ucfirst($state),
                    }),
                TextColumn::make('lead.name')
                    ->label('Linked Lead')
                    ->placeholder('—')
                    ->url(fn ($record) => $record->lead_id
                        ? route('filament.admin.resources.leads.edit', $record->lead_id)
                        : null
                    )
                    ->color('primary')
                    ->toggleable(),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Requested'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'   => '🟡 Pending',
                        'contacted' => '📞 Contacted',
                        'demo_done' => '✅ Demo Done',
                        'converted' => '🚀 Converted',
                        'rejected'  => '❌ Rejected',
                    ]),
                SelectFilter::make('institution_type')
                    ->options([
                        'school'     => 'School',
                        'college'    => 'College',
                        'university' => 'University',
                        'madrasha'   => 'Madrasha',
                    ]),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // Mark Contacted
                Action::make('markContacted')
                    ->label('Contacted')
                    ->icon('heroicon-o-phone')
                    ->color('info')
                    ->action(fn(DemoRequest $record) => $record->update(['status' => 'contacted']))
                    ->visible(fn(DemoRequest $record) => $record->status === 'pending'),

                // Mark Demo Done
                Action::make('markDemoDone')
                    ->label('Demo Done')
                    ->icon('heroicon-o-check-circle')
                    ->color('primary')
                    ->action(fn(DemoRequest $record) => $record->update(['status' => 'demo_done']))
                    ->visible(fn(DemoRequest $record) => in_array($record->status, ['pending', 'contacted'])),

                // ★ Convert to Lead — the key CRM flow action
                Action::make('convertToLead')
                    ->label('→ Convert to Lead')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (DemoRequest $record) => 'Convert "' . $record->institution_name . '" to Lead?')
                    ->modalDescription('This will create a new Lead with all data from this demo request and link them together.')
                    ->modalSubmitActionLabel('Convert to Lead')
                    ->form([
                        Select::make('assigned_to')
                            ->label('Assign Lead To')
                            ->options(User::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Select assignee'),
                        Select::make('team_id')
                            ->label('Team')
                            ->options(Team::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Select team'),
                    ])
                    ->action(function (DemoRequest $record, array $data) {
                        $lead = $record->convertToLead(
                            $data['assigned_to'] ?? null,
                            $data['team_id'] ?? null
                        );

                        Notification::make()
                            ->title('Lead Created!')
                            ->body("Lead \"{$lead->name}\" has been created from this demo request.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn(DemoRequest $record) =>
                        !$record->lead_id && $record->status !== 'rejected'
                    ),

                // Reject
                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(fn(DemoRequest $record) => $record->update(['status' => 'rejected']))
                    ->visible(fn(DemoRequest $record) => !in_array($record->status, ['converted', 'rejected'])),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
