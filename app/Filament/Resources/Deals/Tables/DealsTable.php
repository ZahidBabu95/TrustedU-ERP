<?php

namespace App\Filament\Resources\Deals\Tables;

use App\Models\Deal;
use App\Models\Team;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;

class DealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn ($record) => $record->company),
                TextColumn::make('stage')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'discovery'    => 'gray',
                        'prospecting'  => 'info',
                        'proposal'     => 'warning',
                        'negotiation'  => 'primary',
                        'contract'     => 'primary',
                        'closed_won'   => 'success',
                        'closed_lost'  => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => Deal::STAGE_LABELS[$state] ?? $state)
                    ->icon(fn (string $state): string => match ($state) {
                        'discovery'    => 'heroicon-o-magnifying-glass',
                        'prospecting'  => 'heroicon-o-eye',
                        'proposal'     => 'heroicon-o-document-text',
                        'negotiation'  => 'heroicon-o-scale',
                        'contract'     => 'heroicon-o-clipboard-document-check',
                        'closed_won'   => 'heroicon-o-trophy',
                        'closed_lost'  => 'heroicon-o-x-circle',
                        default        => 'heroicon-o-question-mark-circle',
                    }),
                TextColumn::make('value')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('priority')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'low'    => 'success',
                        'medium' => 'warning',
                        'high'   => 'danger',
                        'urgent' => 'danger',
                        default  => 'gray',
                    }),
                TextColumn::make('probability')
                    ->suffix('%')
                    ->sortable()
                    ->color(fn ($state) => match (true) {
                        $state >= 90 => 'success',
                        $state >= 50 => 'warning',
                        default      => 'gray',
                    }),
                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable()
                    ->placeholder('Unassigned'),
                TextColumn::make('lead.name')
                    ->label('From Lead')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('client.name')
                    ->label('Client')
                    ->sortable()
                    ->placeholder('—')
                    ->url(fn ($record) => $record->client_id
                        ? route('filament.admin.resources.clients.edit', $record->client_id)
                        : null
                    )
                    ->color('primary')
                    ->toggleable(),
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
                SelectFilter::make('stage')
                    ->options(Deal::STAGE_LABELS)
                    ->multiple(),
                SelectFilter::make('priority')
                    ->options(Deal::PRIORITY_LABELS),
                SelectFilter::make('deal_source')
                    ->label('Source')
                    ->options(Deal::SOURCE_LABELS),
                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // Quick stage advancement
                Action::make('advanceStage')
                    ->label(fn (Deal $record) => match ($record->stage) {
                        'discovery'    => '→ Prospecting',
                        'prospecting'  => '→ Proposal',
                        'proposal'     => '→ Negotiation',
                        'negotiation'  => '→ Contract',
                        'contract'     => '🏆 Close Won',
                        default        => '—',
                    })
                    ->icon(fn (Deal $record) => match ($record->stage) {
                        'contract' => 'heroicon-o-trophy',
                        default    => 'heroicon-o-arrow-right',
                    })
                    ->color(fn (Deal $record) => match ($record->stage) {
                        'contract' => 'success',
                        default    => 'info',
                    })
                    ->requiresConfirmation()
                    ->action(function (Deal $record) {
                        $nextStage = match ($record->stage) {
                            'discovery'    => 'prospecting',
                            'prospecting'  => 'proposal',
                            'proposal'     => 'negotiation',
                            'negotiation'  => 'contract',
                            'contract'     => 'closed_won',
                            default        => null,
                        };

                        if ($nextStage) {
                            $updates = [
                                'stage'       => $nextStage,
                                'probability' => Deal::STAGE_PROBABILITIES[$nextStage] ?? 0,
                            ];
                            if ($nextStage === 'closed_won') {
                                $updates['closed_at'] = now();
                            }
                            $record->update($updates);

                            Notification::make()
                                ->title('Stage Updated')
                                ->body("Deal moved to: " . (Deal::STAGE_LABELS[$nextStage] ?? $nextStage))
                                ->success()
                                ->send();
                        }
                    })
                    ->visible(fn (Deal $record) => in_array($record->stage, Deal::KANBAN_STAGES)),

                // Mark Lost
                Action::make('markLost')
                    ->label('Lost')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Deal as Lost?')
                    ->action(function (Deal $record) {
                        $record->update([
                            'stage'       => 'closed_lost',
                            'probability' => 0,
                            'closed_at'   => now(),
                        ]);
                    })
                    ->visible(fn (Deal $record) => !in_array($record->stage, ['closed_won', 'closed_lost'])),

                // ★ Convert to Client — the key CRM flow action
                Action::make('convertToClient')
                    ->label('→ Create Client')
                    ->icon('heroicon-o-building-office-2')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (Deal $record) => 'Create Client from "' . $record->title . '"?')
                    ->modalDescription('This will create a new Client record, close this deal as Won, and link everything together.')
                    ->modalSubmitActionLabel('Create Client')
                    ->form([
                        Select::make('team_id')
                            ->label('Assign Client to Team')
                            ->options(Team::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('Select team'),
                    ])
                    ->action(function (Deal $record, array $data) {
                        $client = $record->convertToClient($data['team_id'] ?? null);

                        Notification::make()
                            ->title('🎉 Client Created!')
                            ->body("Client \"{$client->name}\" has been created! Deal closed as Won.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Deal $record) =>
                        !$record->isConverted()
                        && $record->stage !== 'closed_lost'
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
