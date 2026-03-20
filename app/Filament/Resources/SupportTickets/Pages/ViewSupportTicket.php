<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use App\Models\SupportTicket;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Livewire\WithFileUploads;

class ViewSupportTicket extends ViewRecord
{
    use WithFileUploads;

    protected static string $resource = SupportTicketResource::class;
    protected string $view = 'filament.pages.support-ticket-view';

    public string $replyMessage = '';
    public $replyAttachment = null;
    public bool $isInternal = false;

    public function getTitle(): string
    {
        return $this->record->ticket_number . ' — ' . $this->record->subject;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('resolve')
                ->label('Resolve')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->visible(fn () => in_array($this->record->status, ['open', 'in_progress']))
                ->action(function () {
                    $this->record->resolve();
                    $this->record->addMessage('Ticket marked as resolved.', auth()->id(), 'system');
                    Notification::make()->title('Ticket resolved!')->success()->send();
                    $this->record = $this->record->fresh(['messages.sender', 'client', 'assignee']);
                }),
            Actions\Action::make('close')
                ->label('Close')
                ->icon('heroicon-o-x-circle')
                ->color('gray')
                ->visible(fn () => $this->record->status !== 'closed')
                ->requiresConfirmation()
                ->action(function () {
                    $this->record->close();
                    $this->record->addMessage('Ticket closed.', auth()->id(), 'system');
                    Notification::make()->title('Ticket closed.')->send();
                    $this->record = $this->record->fresh(['messages.sender', 'client', 'assignee']);
                }),
            Actions\Action::make('reopen')
                ->label('Reopen')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->visible(fn () => in_array($this->record->status, ['resolved', 'closed']))
                ->action(function () {
                    $this->record->reopen();
                    $this->record->addMessage('Ticket reopened.', auth()->id(), 'system');
                    Notification::make()->title('Ticket reopened.')->warning()->send();
                    $this->record = $this->record->fresh(['messages.sender', 'client', 'assignee']);
                }),
            Actions\EditAction::make()
                ->url(fn () => SupportTicketResource::getUrl('edit', ['record' => $this->record])),
        ];
    }

    public function sendReply(): void
    {
        if (empty(trim($this->replyMessage))) {
            Notification::make()->title('Message is required.')->danger()->send();
            return;
        }

        $attachmentPath = null;
        if ($this->replyAttachment) {
            $attachmentPath = $this->replyAttachment->store('support-attachments', 'public');
        }

        $this->record->addMessage(
            $this->replyMessage,
            auth()->id(),
            'agent',
            $attachmentPath,
            $this->isInternal
        );

        $this->replyMessage = '';
        $this->replyAttachment = null;
        $this->isInternal = false;

        $this->record = $this->record->fresh(['messages.sender', 'client', 'assignee']);

        Notification::make()->title('Reply sent!')->success()->send();
    }

    public function updateStatus(string $status): void
    {
        $this->record->update(['status' => $status]);
        $this->record = $this->record->fresh(['messages.sender', 'client', 'assignee']);
        Notification::make()->title('Status updated to ' . ucfirst($status))->success()->send();
    }

    public function updatePriority(string $priority): void
    {
        $this->record->update(['priority' => $priority]);
        $this->record = $this->record->fresh(['messages.sender', 'client', 'assignee']);
        Notification::make()->title('Priority updated to ' . ucfirst($priority))->success()->send();
    }
}
