<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\DemoRequest;
use App\Models\Lead;
use App\Models\Task;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Main Dashboard';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = -10;

    // Use our custom Blade view instead of widgets
    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    public function getViewData(): array
    {
        $totalLeads    = Lead::count();
        $revenue       = Client::where('is_active', true)->count() * 4500;
        $activeTickets = DemoRequest::where('status', 'pending')->count();
        $activeClients = Client::where('is_active', true)->count();
        $convRate      = $totalLeads > 0 ? round(($activeClients / $totalLeads) * 100, 1) : 0;

        $recentLeads   = Lead::with('assignee')->latest()->limit(4)->get();
        $upcomingTasks = Task::where('status', '!=', 'completed')
            ->latest()
            ->limit(4)
            ->get();

        return compact(
            'totalLeads', 'revenue', 'activeTickets', 'convRate', 'recentLeads', 'upcomingTasks'
        );
    }

    // No widgets on this page
    public function getWidgets(): array
    {
        return [];
    }
}
