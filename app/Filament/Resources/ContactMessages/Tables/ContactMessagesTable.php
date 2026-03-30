<?php

namespace App\Filament\Resources\ContactMessages\Tables;

use App\Models\CrmActivity;
use App\Models\Lead;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class ContactMessagesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')->searchable()->weight('bold'),
                TextColumn::make('email')->searchable(),
                TextColumn::make('phone')->searchable()->placeholder('—'),
                TextColumn::make('subject')->limit(40)->toggleable(),
                TextColumn::make('status')->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new'      => 'danger',
                        'read'     => 'info',
                        'replied'  => 'success',
                        'archived' => 'gray',
                        default    => 'gray',
                    }),
                TextColumn::make('created_at')->dateTime()->sortable()->label('Received'),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(['new' => 'New', 'read' => 'Read', 'replied' => 'Replied', 'archived' => 'Archived']),
            ])
            ->recordActions([
                EditAction::make(),

                // ★ Convert to Lead
                Action::make('convertToLead')
                    ->label('🚀 Convert to Lead')
                    ->icon('heroicon-o-user-plus')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Convert Message to Lead')
                    ->modalDescription(fn ($record) => "Convert \"{$record->name}\" to a CRM Lead?")
                    ->form([
                        Select::make('source')
                            ->options([
                                'web'       => '🌐 Website',
                                'referral'  => '🤝 Referral',
                                'social'    => '📱 Social Media',
                                'cold_call' => '📞 Cold Call',
                                'email'     => '📧 Email',
                                'other'     => '📋 Other',
                            ])
                            ->default('web')
                            ->native(false)
                            ->required(),
                        Select::make('interest_level')
                            ->options([
                                'cold' => '❄️ Cold',
                                'warm' => '🌤️ Warm',
                                'hot'  => '🔥 Hot',
                            ])
                            ->default('warm')
                            ->native(false)
                            ->required(),
                        Select::make('assigned_to')
                            ->label('Assign To')
                            ->options(User::pluck('name', 'id'))
                            ->searchable()
                            ->preload()
                            ->placeholder('— Unassigned —'),
                    ])
                    ->action(function ($record, array $data) {
                        // Create lead from message
                        $lead = Lead::create([
                            'name'           => $record->name,
                            'email'          => $record->email,
                            'phone'          => $record->phone,
                            'source'         => $data['source'],
                            'interest_level' => $data['interest_level'],
                            'assigned_to'    => $data['assigned_to'] ?? null,
                            'status'         => 'new',
                            'pipeline_stage' => 'new_lead',
                            'notes'          => "Converted from website message.\n\nSubject: {$record->subject}\n\nMessage: {$record->message}",
                        ]);

                        // Mark message as read
                        $record->update(['status' => 'replied']);

                        // Log activity
                        CrmActivity::log(
                            'lead', $lead->id, 'note',
                            "Lead created from website contact message",
                            "Name: {$record->name}, Subject: {$record->subject}",
                            ['message_id' => $record->id]
                        );

                        Notification::make()
                            ->title("🚀 Lead Created!")
                            ->body("{$record->name} has been converted to a lead.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn ($record) => $record->status !== 'archived'),
            ])
            ->toolbarActions([BulkActionGroup::make([DeleteBulkAction::make()])]);
    }
}
