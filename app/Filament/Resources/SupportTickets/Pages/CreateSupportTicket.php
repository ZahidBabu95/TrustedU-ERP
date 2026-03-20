<?php

namespace App\Filament\Resources\SupportTickets\Pages;

use App\Filament\Resources\SupportTickets\SupportTicketResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Support\Enums\Width;

class CreateSupportTicket extends CreateRecord
{
    protected static string $resource = SupportTicketResource::class;

    protected Width|string|null $maxContentWidth = 'full';

    /**
     * @return string[]
     */
    public function getPageClasses(): array
    {
        return ['fi-support-ticket-create'];
    }
}
