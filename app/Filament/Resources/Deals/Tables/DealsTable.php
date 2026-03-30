<?php

namespace App\Filament\Resources\Deals\Tables;

use App\Models\CrmActivity;
use App\Models\Deal;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class DealsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('deed_number')
                    ->label('Deed #')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->placeholder('Not Generated')
                    ->icon('heroicon-o-document-check')
                    ->copyable(),
                TextColumn::make('title')
                    ->label('Agreement Title')
                    ->searchable()
                    ->sortable()
                    ->limit(40)
                    ->description(fn ($record) => $record->company),
                TextColumn::make('deed_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Deal::DEED_STATUS_LABELS[$state] ?? 'Not Created')
                    ->color(fn (?string $state): string => match ($state) {
                        'not_created' => 'gray',
                        'draft'       => 'warning',
                        'generated'   => 'info',
                        'signed'      => 'success',
                        'active'      => 'success',
                        'expired'     => 'danger',
                        default       => 'gray',
                    }),
                TextColumn::make('deed_plan_name')
                    ->label('Plan')
                    ->placeholder('—')
                    ->icon('heroicon-o-rectangle-stack'),
                TextColumn::make('deed_monthly_fee')
                    ->label('Monthly Fee')
                    ->money('BDT')
                    ->sortable(),
                TextColumn::make('deed_total_users')
                    ->label('Users')
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('deed_effective_date')
                    ->label('Start Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('deed_end_date')
                    ->label('End Date')
                    ->date('d M Y')
                    ->sortable()
                    ->placeholder('—'),
                TextColumn::make('deed_duration')
                    ->label('Duration')
                    ->suffix(' Year(s)')
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
                    ->color('primary'),
                TextColumn::make('assignee.name')
                    ->label('Assigned To')
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('deed_generated_at')
                    ->label('Generated')
                    ->since()
                    ->sortable()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('deed_status')
                    ->label('Deed Status')
                    ->options(Deal::DEED_STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                EditAction::make(),

                // ★ Generate Deed
                Action::make('generateDeed')
                    ->label('📜 Generate Deed')
                    ->icon('heroicon-o-document-check')
                    ->color('primary')
                    ->requiresConfirmation()
                    ->modalHeading('Generate Software Service Agreement?')
                    ->modalDescription('এটি ডীড নম্বর তৈরি করবে। এরপর Edit করে তথ্য সম্পাদনা ও প্রিন্ট করতে পারবেন।')
                    ->action(function (Deal $record) {
                        $year = now()->format('Y');
                        $count = Deal::where('deed_number', 'like', "DEED-{$year}-%")->count();
                        $deedNumber = "DEED-{$year}-" . str_pad($count + 1, 4, '0', STR_PAD_LEFT);

                        $updateData = ['deed_number' => $deedNumber, 'deed_status' => 'draft'];

                        // Auto-fill from client if available
                        if ($record->client) {
                            $updateData['deed_client_representative'] = $record->client->principal_name ?? $record->contact_name;
                            $updateData['deed_client_address'] = $record->client->address;
                        }

                        if (empty($record->deed_effective_date)) {
                            $updateData['deed_effective_date'] = now();
                            $updateData['deed_end_date'] = now()->addYears(2);
                            $updateData['deed_duration'] = '2';
                        }

                        $record->update($updateData);

                        CrmActivity::log('deal', $record->id, 'system',
                            "📜 Deed {$deedNumber} generated",
                            'Software Service Agreement created'
                        );

                        Notification::make()
                            ->title("📜 Deed {$deedNumber} Generated!")
                            ->body('Edit করে সম্পূর্ণ তথ্য দিন, তারপর Print করুন।')
                            ->success()
                            ->send();
                    })
                    ->visible(fn (Deal $record) => in_array($record->deed_status, ['not_created', null])),

                // ★ Print Deed
                Action::make('printDeed')
                    ->label('🖨️ Print')
                    ->icon('heroicon-o-printer')
                    ->color('gray')
                    ->url(fn (Deal $record) => route('crm.deed.print', $record->id), shouldOpenInNewTab: true)
                    ->action(function (Deal $record) {
                        if ($record->deed_status === 'draft') {
                            $record->update(['deed_status' => 'generated', 'deed_generated_at' => now()]);
                        }
                    })
                    ->visible(fn (Deal $record) => !in_array($record->deed_status, ['not_created', null])),

                // ★ Mark as Signed
                Action::make('markSigned')
                    ->label('🖊️ Mark Signed')
                    ->icon('heroicon-o-pencil-square')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Mark Deed as Signed?')
                    ->action(function (Deal $record) {
                        $record->update(['deed_status' => 'signed']);

                        CrmActivity::log('deal', $record->id, 'status_change',
                            "🖊️ Deed {$record->deed_number} marked as Signed"
                        );

                        Notification::make()->title('✅ Deed Signed!')->success()->send();
                    })
                    ->visible(fn (Deal $record) => $record->deed_status === 'generated'),

                // ★ Mark Active
                Action::make('markActive')
                    ->label('🟢 Activate')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->action(function (Deal $record) {
                        $record->update(['deed_status' => 'active']);

                        CrmActivity::log('deal', $record->id, 'status_change',
                            "🟢 Deed {$record->deed_number} activated"
                        );

                        Notification::make()->title('🟢 Deed Activated!')->success()->send();
                    })
                    ->visible(fn (Deal $record) => $record->deed_status === 'signed'),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }
}
