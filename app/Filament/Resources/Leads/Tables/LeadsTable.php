<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Models\Lead;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->company),
                TextColumn::make('email')
                    ->searchable()
                    ->copyable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->searchable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new'          => 'gray',
                        'contacted'    => 'warning',
                        'qualified'    => 'primary',
                        'proposal'     => 'info',
                        'negotiation'  => 'primary',
                        'won'          => 'success',
                        'lost'         => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => Lead::STATUS_LABELS[$state] ?? $state)
                    ->icon(fn (string $state): string => match ($state) {
                        'new'          => 'heroicon-o-sparkles',
                        'contacted'    => 'heroicon-o-phone',
                        'qualified'    => 'heroicon-o-check-badge',
                        'proposal'     => 'heroicon-o-document-text',
                        'negotiation'  => 'heroicon-o-scale',
                        'won'          => 'heroicon-o-trophy',
                        'lost'         => 'heroicon-o-x-circle',
                        default        => 'heroicon-o-question-mark-circle',
                    }),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'success',
                        'medium' => 'warning',
                        'high'   => 'danger',
                        'urgent' => 'danger',
                        default  => 'gray',
                    }),
                TextColumn::make('value')
                    ->money('BDT')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable()
                    ->placeholder('Unassigned'),
                TextColumn::make('source')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => Lead::SOURCE_LABELS[$state] ?? $state)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('expected_close_date')
                    ->date()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(Lead::STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('source')
                    ->options(Lead::SOURCE_LABELS),
                SelectFilter::make('priority')
                    ->options(Lead::PRIORITY_LABELS),
                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // Quick status advancement
                Action::make('advanceStatus')
                    ->label(fn (Lead $record) => match ($record->status) {
                        'new'         => '→ Contacted',
                        'contacted'   => '→ Qualified',
                        'qualified'   => '→ Proposal',
                        'proposal'    => '→ Negotiation',
                        'negotiation' => '🏆 Won',
                        default       => '—',
                    })
                    ->icon(fn (Lead $record) => match ($record->status) {
                        'negotiation' => 'heroicon-o-trophy',
                        default       => 'heroicon-o-arrow-right',
                    })
                    ->color(fn (Lead $record) => match ($record->status) {
                        'negotiation' => 'success',
                        default       => 'info',
                    })
                    ->requiresConfirmation()
                    ->action(function (Lead $record) {
                        $nextStatus = match ($record->status) {
                            'new'         => 'contacted',
                            'contacted'   => 'qualified',
                            'qualified'   => 'proposal',
                            'proposal'    => 'negotiation',
                            'negotiation' => 'won',
                            default       => null,
                        };

                        if ($nextStatus) {
                            $record->update(['status' => $nextStatus]);
                            Notification::make()
                                ->title('Status Updated')
                                ->body("Lead moved to: " . (Lead::STATUS_LABELS[$nextStatus] ?? $nextStatus))
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn (Lead $record) => in_array($record->status, ['new', 'contacted', 'qualified', 'proposal', 'negotiation'])),

                // Mark Lost
                Action::make('markLost')
                    ->label('Lost')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Lead as Lost?')
                    ->action(fn (Lead $record) => $record->update(['status' => 'lost']))
                    ->visible(fn (Lead $record) => !in_array($record->status, ['won', 'lost'])),

                // ★ Convert to Deal — the key CRM flow action
                Action::make('convertToDeal')
                    ->label('→ Create Deal')
                    ->icon('heroicon-o-rocket-launch')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Lead $record) => 'Convert "' . $record->name . '" to Deal?')
                    ->modalDescription('This will create a new Deal and link it to this lead. The lead will be marked as Won.')
                    ->modalSubmitActionLabel('Create Deal')
                    ->form([
                        TextInput::make('value')
                            ->label('Deal Value')
                            ->numeric()
                            ->prefix('৳')
                            ->default(fn (Lead $record) => $record->value),
                        Select::make('assigned_to')
                            ->label('Assign Deal To')
                            ->options(User::where('is_active', true)->pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->default(fn (Lead $record) => $record->assigned_to),
                    ])
                    ->action(function (Lead $record, array $data) {
                        if ($data['value'] ?? null) {
                            $record->update(['value' => $data['value']]);
                        }

                        $deal = $record->convertToDeal($data['assigned_to'] ?? null);

                        Notification::make()
                            ->title('Deal Created!')
                            ->body("Deal \"{$deal->title}\" has been created from this lead.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Lead $record) =>
                        !$record->isConverted()
                        && !in_array($record->status, ['lost'])
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
