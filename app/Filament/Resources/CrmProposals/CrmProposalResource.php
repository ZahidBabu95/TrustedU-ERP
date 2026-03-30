<?php

namespace App\Filament\Resources\CrmProposals;

use App\Filament\Resources\CrmProposals\Pages\CreateCrmProposal;
use App\Filament\Resources\CrmProposals\Pages\EditCrmProposal;
use App\Filament\Resources\CrmProposals\Pages\ListCrmProposals;
use App\Models\CrmActivity;
use App\Models\CrmProposal;
use App\Models\Deal;
use App\Models\ErpModule;
use App\Models\Lead;
use App\Models\User;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class CrmProposalResource extends Resource
{
    protected static ?string $model = CrmProposal::class;

    public static function getNavigationIcon(): string|BackedEnum|null { return 'heroicon-o-document-text'; }
    public static function getNavigationGroup(): ?string { return 'CRM'; }
    public static function getNavigationLabel(): string { return 'Proposals'; }
    protected static ?int $navigationSort = 2;

    public static function getNavigationBadge(): ?string
    {
        $count = CrmProposal::whereIn('status', ['draft', 'sent'])->count();
        return $count > 0 ? (string) $count : null;
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Tabs::make('Proposal')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->schema([

                    // ━━ TAB 1: Proposal Info ━━
                    Tab::make('Proposal Info')
                        ->icon('heroicon-o-document-text')
                        ->schema([
                            Section::make('Basic Information')
                                ->columns(2)
                                ->schema([
                                    Select::make('lead_id')
                                        ->label('Lead')
                                        ->options(Lead::whereNotIn('status', ['lost'])
                                            ->pluck('name', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->nullable()
                                        ->prefixIcon('heroicon-o-user')
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if ($state) {
                                                $lead = Lead::find($state);
                                                if ($lead) {
                                                    $pd = $lead->proposal_data ?? [];
                                                    $set('title', $pd['title'] ?? 'Proposal - ' . $lead->name);
                                                    $set('base_price', $pd['base_price'] ?? $lead->value);
                                                    if (!empty($pd['modules'])) {
                                                        $set('modules_included', $pd['modules']);
                                                    }
                                                }
                                            }
                                        }),
                                    Select::make('deal_id')
                                        ->label('Deal (Optional)')
                                        ->options(Deal::whereNotIn('stage', ['closed_lost'])
                                            ->pluck('title', 'id'))
                                        ->searchable()
                                        ->preload()
                                        ->nullable()
                                        ->prefixIcon('heroicon-o-briefcase')
                                        ->live()
                                        ->afterStateUpdated(function ($state, $set) {
                                            if ($state) {
                                                $deal = Deal::find($state);
                                                if ($deal) {
                                                    $set('title', $deal->title . ' — Proposal');
                                                    $set('base_price', $deal->value);
                                                    if ($deal->modules_required) {
                                                        $set('modules_included', $deal->modules_required);
                                                    }
                                                }
                                            }
                                        }),
                                    TextInput::make('version')
                                        ->numeric()
                                        ->default(1)
                                        ->disabled()
                                        ->prefixIcon('heroicon-o-hashtag'),
                                    TextInput::make('title')
                                        ->required()
                                        ->maxLength(255)
                                        ->columnSpanFull()
                                        ->prefixIcon('heroicon-o-document-text'),
                                    Select::make('status')
                                        ->options(CrmProposal::STATUS_LABELS)
                                        ->default('draft')
                                        ->native(false)
                                        ->prefixIcon('heroicon-o-flag'),
                                ]),
                        ]),

                    // ━━ TAB 2: Modules & Pricing ━━
                    Tab::make('Pricing')
                        ->icon('heroicon-o-calculator')
                        ->schema([
                            Section::make('ERP Modules Included')
                                ->schema([
                                    Select::make('modules_included')
                                        ->label('Included Modules')
                                        ->options(ErpModule::active()->ordered()->pluck('name', 'slug'))
                                        ->multiple()
                                        ->searchable()
                                        ->preload()
                                        ->prefixIcon('heroicon-o-squares-2x2')
                                        ->columnSpanFull(),
                                ]),

                            Section::make('Pricing')
                                ->columns(2)
                                ->description('মূল্য ও ডিসকাউন্ট')
                                ->schema([
                                    TextInput::make('base_price')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->label('Base Price')
                                        ->required()
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, $get, $set) {
                                            $discountPct = floatval($get('discount_percent') ?? 0);
                                            $discountAmt = $discountPct > 0
                                                ? (floatval($state) * $discountPct / 100)
                                                : floatval($get('discount_amount') ?? 0);
                                            $set('discount_amount', round($discountAmt, 2));
                                            $set('final_price', max(0, round(floatval($state) - $discountAmt, 2)));
                                        }),
                                    TextInput::make('discount_percent')
                                        ->numeric()
                                        ->suffix('%')
                                        ->label('Discount %')
                                        ->default(0)
                                        ->minValue(0)
                                        ->maxValue(100)
                                        ->live(onBlur: true)
                                        ->afterStateUpdated(function ($state, $get, $set) {
                                            $base = floatval($get('base_price') ?? 0);
                                            $discountAmt = $base * floatval($state) / 100;
                                            $set('discount_amount', round($discountAmt, 2));
                                            $set('final_price', max(0, round($base - $discountAmt, 2)));
                                        }),
                                    TextInput::make('discount_amount')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->label('Discount Amount')
                                        ->disabled()
                                        ->dehydrated(),
                                    TextInput::make('final_price')
                                        ->numeric()
                                        ->prefix('৳')
                                        ->label('Final Price')
                                        ->disabled()
                                        ->dehydrated()
                                        ->prefixIcon('heroicon-o-banknotes'),
                                ]),
                        ]),

                    // ━━ TAB 3: Terms ━━
                    Tab::make('Terms')
                        ->icon('heroicon-o-clipboard-document-list')
                        ->schema([
                            Section::make('Implementation & Payment Terms')
                                ->columns(2)
                                ->schema([
                                    TextInput::make('implementation_days')
                                        ->numeric()
                                        ->suffix('days')
                                        ->label('Implementation Period')
                                        ->default(30)
                                        ->prefixIcon('heroicon-o-clock'),
                                    TextInput::make('validity_days')
                                        ->numeric()
                                        ->suffix('days')
                                        ->label('Proposal Validity')
                                        ->default(15)
                                        ->prefixIcon('heroicon-o-calendar'),
                                    Textarea::make('payment_terms')
                                        ->label('Payment Terms')
                                        ->rows(3)
                                        ->placeholder('e.g. 50% upfront, 50% after go-live')
                                        ->columnSpanFull(),
                                    Textarea::make('notes')
                                        ->label('Additional Notes')
                                        ->rows(3)
                                        ->columnSpanFull(),
                                ]),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->prefix('P-')
                    ->sortable()
                    ->searchable()
                    ->alignCenter()
                    ->size('xs')
                    ->width('60px'),
                TextColumn::make('lead.name')
                    ->label('Lead')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->placeholder('—')
                    ->icon('heroicon-o-user')
                    ->url(fn ($record) => $record->lead_id
                        ? route('filament.admin.resources.leads.view', ['record' => $record->lead_id])
                        : null),
                TextColumn::make('deal.title')
                    ->label('Deal')
                    ->searchable()
                    ->sortable()
                    ->placeholder('—')
                    ->description(fn ($record) => $record->deal?->company)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('version')
                    ->label('V')
                    ->prefix('v')
                    ->sortable()
                    ->alignCenter()
                    ->size('xs'),
                TextColumn::make('title')
                    ->searchable()
                    ->wrap()
                    ->lineClamp(2)
                    ->weight('medium'),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (string $state) => CrmProposal::STATUS_LABELS[$state] ?? $state)
                    ->color(fn (string $state): string => match ($state) {
                        'draft'       => 'gray',
                        'sent'        => 'info',
                        'negotiation' => 'warning',
                        'approved'    => 'success',
                        'rejected'    => 'danger',
                        'expired'     => 'gray',
                        default       => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'draft'       => 'heroicon-o-pencil',
                        'sent'        => 'heroicon-o-paper-airplane',
                        'negotiation' => 'heroicon-o-scale',
                        'approved'    => 'heroicon-o-check-circle',
                        'rejected'    => 'heroicon-o-x-circle',
                        'expired'     => 'heroicon-o-clock',
                        default       => 'heroicon-o-document',
                    }),
                TextColumn::make('base_price')
                    ->money('BDT')
                    ->label('Base')
                    ->sortable(),
                TextColumn::make('discount_percent')
                    ->suffix('%')
                    ->label('Disc')
                    ->placeholder('—')
                    ->alignCenter()
                    ->size('xs'),
                TextColumn::make('final_price')
                    ->money('BDT')
                    ->label('Final Price')
                    ->sortable()
                    ->weight('bold')
                    ->color('success'),
                TextColumn::make('implementation_days')
                    ->label('Days')
                    ->suffix(' d')
                    ->placeholder('—')
                    ->alignCenter()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('creator.name')
                    ->label('Created By')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('sent_at')
                    ->label('Sent')
                    ->date()
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('created_at')
                    ->since()
                    ->sortable()
                    ->label('Created')
                    ->size('xs'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options(CrmProposal::STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('lead_id')
                    ->label('Lead')
                    ->options(Lead::pluck('name', 'id'))
                    ->searchable(),
                SelectFilter::make('deal_id')
                    ->label('Deal')
                    ->options(Deal::pluck('title', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),

                // ★ Preview Proposal PDF
                Action::make('preview')
                    ->label('📄 Preview')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (CrmProposal $record) => $record->lead_id
                        ? route('lead.proposal-report', ['lead' => $record->lead_id])
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn (CrmProposal $record) => $record->lead_id !== null),

                // ★ Send Proposal
                Action::make('send')
                    ->label('📧 Send')
                    ->icon('heroicon-o-paper-airplane')
                    ->color('info')
                    ->requiresConfirmation()
                    ->modalHeading('Send this Proposal?')
                    ->modalDescription('The proposal status will be changed to "Sent" and the date will be recorded.')
                    ->action(function (CrmProposal $record) {
                        $record->markSent();

                        // Update deal pipeline stage if deal exists
                        if ($record->deal) {
                            $record->deal->update(['pipeline_stage' => 'proposal_sent']);
                        }

                        // Log activity
                        $entityType = $record->lead_id ? 'lead' : 'deal';
                        $entityId = $record->lead_id ?: $record->deal_id;
                        if ($entityId) {
                            CrmActivity::log($entityType, $entityId, 'stage_change',
                                "Proposal v{$record->version} sent to client",
                                "Amount: ৳{$record->final_price}",
                                ['proposal_id' => $record->id]
                            );
                        }

                        Notification::make()
                            ->title('📧 Proposal Sent!')
                            ->body("Proposal v{$record->version} marked as sent.")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmProposal $record) => $record->status === 'draft'),

                // ★ Approve Proposal
                Action::make('approve')
                    ->label('✅ Approve')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->modalHeading('Approve this Proposal?')
                    ->modalDescription('Client has accepted this proposal.')
                    ->action(function (CrmProposal $record) {
                        $record->markApproved();

                        // Advance deal if exists
                        if ($record->deal) {
                            $record->deal->update([
                                'pipeline_stage' => 'negotiation',
                                'stage'          => 'negotiation',
                                'probability'    => 70,
                                'value'          => $record->final_price,
                            ]);
                        }

                        $entityType = $record->lead_id ? 'lead' : 'deal';
                        $entityId = $record->lead_id ?: $record->deal_id;
                        if ($entityId) {
                            CrmActivity::log($entityType, $entityId, 'conversion',
                                "🎉 Proposal v{$record->version} APPROVED!",
                                "Final Price: ৳{$record->final_price}.",
                                ['proposal_id' => $record->id, 'final_price' => $record->final_price]
                            );
                        }

                        Notification::make()
                            ->title('🎉 Proposal Approved!')
                            ->body("Final Price: ৳" . number_format($record->final_price))
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmProposal $record) => in_array($record->status, ['sent', 'negotiation'])),

                // Reject
                Action::make('reject')
                    ->label('❌ Reject')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('Reject this Proposal?')
                    ->form([
                        Textarea::make('rejection_reason')
                            ->label('Reason')
                            ->required()
                            ->rows(3),
                    ])
                    ->action(function (CrmProposal $record, array $data) {
                        $record->update([
                            'status'      => 'rejected',
                            'rejected_at' => now(),
                            'notes'       => ($record->notes ? $record->notes . "\n" : '') . "Rejected: " . $data['rejection_reason'],
                        ]);

                        $entityType = $record->lead_id ? 'lead' : 'deal';
                        $entityId = $record->lead_id ?: $record->deal_id;
                        if ($entityId) {
                            CrmActivity::log($entityType, $entityId, 'status_change',
                                "Proposal v{$record->version} rejected",
                                $data['rejection_reason'],
                                ['proposal_id' => $record->id]
                            );
                        }

                        Notification::make()
                            ->title('Proposal Rejected')
                            ->warning()
                            ->send();
                    })
                    ->visible(fn (CrmProposal $record) => in_array($record->status, ['sent', 'negotiation'])),

                // Create New Version
                Action::make('newVersion')
                    ->label('📋 New Version')
                    ->icon('heroicon-o-document-duplicate')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Create New Proposal Version?')
                    ->modalDescription('A new draft will be created based on this proposal with an incremented version number.')
                    ->action(function (CrmProposal $record) {
                        $newProposal = $record->createNewVersion();

                        $entityType = $record->lead_id ? 'lead' : 'deal';
                        $entityId = $record->lead_id ?: $record->deal_id;
                        if ($entityId) {
                            CrmActivity::log($entityType, $entityId, 'note',
                                "New proposal version v{$newProposal->version} created",
                                "Based on v{$record->version}",
                                ['new_proposal_id' => $newProposal->id, 'old_proposal_id' => $record->id]
                            );
                        }

                        Notification::make()
                            ->title("Proposal v{$newProposal->version} Created")
                            ->body("New draft created from v{$record->version}")
                            ->success()
                            ->send();
                    })
                    ->visible(fn (CrmProposal $record) => in_array($record->status, ['rejected', 'expired'])),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCrmProposals::route('/'),
            'create' => CreateCrmProposal::route('/create'),
            'edit'   => EditCrmProposal::route('/{record}/edit'),
        ];
    }
}
