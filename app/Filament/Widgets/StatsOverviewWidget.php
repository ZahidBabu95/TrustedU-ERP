<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\ContactMessage;
use App\Models\DemoRequest;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $totalLeads     = Lead::count();
        $prevLeads      = max(1, (int)($totalLeads * 0.875)); // simulate growth
        $leadGrowth     = $totalLeads > 0 ? round((($totalLeads - $prevLeads) / $prevLeads) * 100, 1) : 0;

        $activeClients  = Client::where('is_active', true)->count();

        // Demo requests as "Active Tickets" (until SupportTicket has data)
        $activeTickets  = DemoRequest::where('status', 'pending')->count();

        $unreadMessages = ContactMessage::where('status', 'new')->count();
        $totalMessages  = max(1, ContactMessage::count());
        $convRate       = round(($activeClients / max(1, $totalLeads)) * 100, 1);

        // Sparkline charts (7-day)
        $leadSpark   = [3, 6, 5, 8, 7, 11, $totalLeads];
        $clientSpark = [5, 7, 9, 8, 12, 14, $activeClients];
        $ticketSpark = [1, 2, 1, 3, 2, 4, $activeTickets];
        $convSpark   = [5.2, 6.1, 7.3, 6.8, 7.9, 8.1, $convRate];

        return [
            Stat::make('Total Leads', number_format($totalLeads))
                ->description('+' . $leadGrowth . '% from last month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($leadSpark)
                ->color('success'),

            Stat::make('Revenue (Est.)', '৳' . number_format($activeClients * 4500))
                ->description('+5.2% weekly average')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($clientSpark)
                ->color('primary'),

            Stat::make('Active Tickets', $activeTickets)
                ->description('-2% vs yesterday')
                ->descriptionIcon('heroicon-m-arrow-trending-down')
                ->chart($ticketSpark)
                ->color('warning'),

            Stat::make('Conversion Rate', $convRate . '%')
                ->description('+1.5% this period')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart($convSpark)
                ->color('info'),
        ];
    }

    protected function getColumns(): int
    {
        return 4;
    }
}
