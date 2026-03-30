<?php

namespace App\Filament\Resources\Leads\Tables;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Support\HtmlString;

class LeadsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->formatStateUsing(fn ($state) => 'L-' . str_pad($state, 4, '0', STR_PAD_LEFT))
                    ->searchable()
                    ->sortable()
                    ->width(70)
                    ->color('gray')
                    ->size('sm'),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->size('sm')
                    ->description(fn ($record) => $record->institute_name ?: $record->company)
                    ->color('primary'),
                TextColumn::make('phone')
                    ->searchable()
                    ->copyable()
                    ->size('sm')
                    ->placeholder('—'),
                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'new'          => 'gray',
                        'contacted'    => 'info',
                        'qualified'    => 'primary',
                        'proposal'     => 'warning',
                        'negotiation'  => 'info',
                        'won'          => 'success',
                        'lost'         => 'danger',
                        default        => 'gray',
                    })
                    ->formatStateUsing(fn (string $state) => Lead::STATUS_LABELS[$state] ?? ucfirst($state))
                    ->icon(fn (string $state): ?string => match ($state) {
                        'new'          => 'heroicon-o-sparkles',
                        'contacted'    => 'heroicon-o-phone',
                        'qualified'    => 'heroicon-o-check-badge',
                        'proposal'     => 'heroicon-o-document-text',
                        'negotiation'  => 'heroicon-o-chat-bubble-left-right',
                        'won'          => 'heroicon-o-trophy',
                        'lost'         => 'heroicon-o-x-circle',
                        default        => null,
                    }),
                TextColumn::make('interest_level')
                    ->label('Interest')
                    ->badge()
                    ->formatStateUsing(fn (?string $state) => Lead::INTEREST_LABELS[$state] ?? $state)
                    ->color(fn (?string $state): string => match ($state) {
                        'hot'  => 'danger',
                        'warm' => 'warning',
                        'cold' => 'gray',
                        default => 'gray',
                    }),
                TextColumn::make('source')
                    ->badge()
                    ->size('sm')
                    ->formatStateUsing(fn (string $state) => Lead::SOURCE_LABELS[$state] ?? $state)
                    ->color('primary'),
                TextColumn::make('value')
                    ->money('BDT')
                    ->sortable()
                    ->size('sm')
                    ->placeholder('—'),
                TextColumn::make('assignee.name')
                    ->label('Assigned')
                    ->sortable()
                    ->size('sm')
                    ->placeholder('—'),
                TextColumn::make('status_changed_at')
                    ->label('Updated')
                    ->since()
                    ->sortable()
                    ->size('sm')
                    ->placeholder(fn ($record) => $record->created_at?->diffForHumans()),
            ])
            ->filters([
                TrashedFilter::make(),
                SelectFilter::make('status')
                    ->options(Lead::STATUS_LABELS)
                    ->multiple(),
                SelectFilter::make('interest_level')
                    ->label('Interest Level')
                    ->options(Lead::INTEREST_LABELS),
                SelectFilter::make('source')
                    ->options(Lead::SOURCE_LABELS),
                SelectFilter::make('assigned_to')
                    ->label('Assigned To')
                    ->options(User::where('is_active', true)->pluck('name', 'id'))
                    ->searchable(),
            ])
            ->recordActions([
                // ── Quick View & Next Step Modal ──
                Action::make('quickView')
                    ->label('')
                    ->icon('heroicon-o-arrow-right-circle')
                    ->color('primary')
                    ->modalHeading(fn (Lead $record) => '📋 ' . $record->name . ' — Quick View')
                    ->modalDescription(fn (Lead $record) => new HtmlString(self::buildModalSummary($record)))
                    ->modalWidth('lg')
                    ->modalIcon('heroicon-o-bolt')
                    ->form(fn (Lead $record) => [
                        Select::make('status')
                            ->label('Update Status')
                            ->options(Lead::STATUS_LABELS)
                            ->default($record->status)
                            ->native(false)
                            ->prefixIcon('heroicon-o-signal'),
                        Select::make('pipeline_stage')
                            ->label('Pipeline Stage')
                            ->options(Lead::PIPELINE_STAGE_LABELS)
                            ->default($record->pipeline_stage)
                            ->native(false)
                            ->prefixIcon('heroicon-o-funnel'),
                        Select::make('interest_level')
                            ->label('Interest Level')
                            ->options(Lead::INTEREST_LABELS)
                            ->default($record->interest_level)
                            ->native(false)
                            ->prefixIcon('heroicon-o-fire'),
                        DatePicker::make('follow_up_date')
                            ->label('Next Follow-up')
                            ->default($record->follow_up_date)
                            ->native(false)
                            ->prefixIcon('heroicon-o-calendar'),
                        Textarea::make('follow_up_notes')
                            ->label('Quick Note')
                            ->default($record->follow_up_notes)
                            ->rows(2)
                            ->placeholder('Add a quick note...'),
                    ])
                    ->modalSubmitActionLabel('Update & Save')
                    ->extraModalFooterActions(fn (Lead $record) => [
                        Action::make('viewFull')
                            ->label('Full Details →')
                            ->url(LeadResource::getUrl('view', ['record' => $record]))
                            ->color('gray'),
                    ])
                    ->action(function (Lead $record, array $data) {
                        $oldStatus = $record->status;
                        $record->update([
                            'status'          => $data['status'],
                            'pipeline_stage'  => $data['pipeline_stage'],
                            'interest_level'  => $data['interest_level'],
                            'follow_up_date'  => $data['follow_up_date'],
                            'follow_up_notes' => $data['follow_up_notes'],
                            'status_changed_at' => $data['status'] !== $oldStatus ? now() : $record->status_changed_at,
                        ]);

                        $statusLabel = Lead::STATUS_LABELS[$data['status']] ?? $data['status'];
                        Notification::make()
                            ->title('Lead Updated')
                            ->body("Status: {$statusLabel}")
                            ->success()
                            ->send();
                    }),
            ])
            ->recordAction('quickView')
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50]);
    }

    /**
     * Build the HTML summary card for the modal header.
     */
    private static function buildModalSummary(Lead $record): string
    {
        $statusLabel    = Lead::STATUS_LABELS[$record->status] ?? ucfirst($record->status);
        $statusColor    = Lead::STATUS_COLORS[$record->status] ?? '#94a3b8';
        $interestLabel  = Lead::INTEREST_LABELS[$record->interest_level] ?? '—';
        $interestColor  = Lead::INTEREST_COLORS[$record->interest_level] ?? '#94a3b8';
        $sourceLabel    = Lead::SOURCE_LABELS[$record->source] ?? '—';
        $pipelineLabel  = Lead::PIPELINE_STAGE_LABELS[$record->pipeline_stage] ?? '—';
        $assigneeName   = $record->assignee?->name ?? '—';
        $leadId         = 'L-' . str_pad($record->id, 4, '0', STR_PAD_LEFT);
        $phone          = $record->phone ?: '—';
        $email          = $record->email ?: '—';
        $institute      = $record->institute_name ?: ($record->company ?: '—');
        $value          = $record->value ? '৳' . number_format($record->value) : '—';
        $followUp       = $record->follow_up_date ? $record->follow_up_date->format('d M Y') : '—';
        $created        = $record->created_at?->format('d M Y');

        return <<<HTML
        <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:12px 16px;margin-bottom:4px;font-size:13px;line-height:1.6">
            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:8px">
                <span style="font-weight:700;color:#1e293b;font-size:14px">{$leadId}</span>
                <span style="display:inline-flex;align-items:center;gap:4px">
                    <span style="background:{$statusColor};color:#fff;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600">{$statusLabel}</span>
                    <span style="background:{$interestColor};color:#fff;padding:2px 10px;border-radius:20px;font-size:11px;font-weight:600">{$interestLabel}</span>
                </span>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:4px 16px;color:#475569">
                <div>📞 <strong>Phone:</strong> {$phone}</div>
                <div>📧 <strong>Email:</strong> {$email}</div>
                <div>🏫 <strong>Institute:</strong> {$institute}</div>
                <div>💰 <strong>Value:</strong> {$value}</div>
                <div>📡 <strong>Source:</strong> {$sourceLabel}</div>
                <div>👤 <strong>Assigned:</strong> {$assigneeName}</div>
                <div>📊 <strong>Pipeline:</strong> {$pipelineLabel}</div>
                <div>📅 <strong>Follow-up:</strong> {$followUp}</div>
            </div>
            <div style="margin-top:6px;color:#94a3b8;font-size:11px;text-align:right">Created: {$created}</div>
        </div>
        HTML;
    }
}
