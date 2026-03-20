<?php

namespace App\Filament\Pages;

use App\Models\Client;
use App\Models\ContactMessage;
use App\Models\Deal;
use App\Models\DemoRequest;
use App\Models\Lead;
use App\Models\SupportTicket;
use App\Models\Task;
use Carbon\Carbon;
use Filament\Pages\Dashboard as BaseDashboard;
use Illuminate\Support\Facades\Auth;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Dashboard';
    protected static ?string $title = 'Dashboard';
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = -10;

    public function getView(): string
    {
        return 'filament.pages.dashboard';
    }

    public function getViewData(): array
    {
        $user   = Auth::user();
        $teamId = $user?->current_team_id;

        // Helper: apply team scope if a specific team is selected
        // Client uses pivot table (client_team), others use direct team_id column
        $scope = function ($query) use ($teamId) {
            if ($teamId) {
                $model = $query->getModel();
                // Client uses belongsToMany teams via pivot table
                if ($model instanceof Client) {
                    return $query->whereHas('teams', fn($q) => $q->where('teams.id', $teamId));
                }
                // Other models have a direct team_id column
                $table = $model->getTable();
                if (\Illuminate\Support\Facades\Schema::hasColumn($table, 'team_id')) {
                    return $query->where("{$table}.team_id", $teamId);
                }
            }
            return $query;
        };

        // ── Core KPIs ──
        $totalLeads      = $scope(Lead::query())->count();
        $newLeadsToday   = $scope(Lead::whereDate('created_at', today()))->count();
        $newLeadsWeek    = $scope(Lead::where('created_at', '>=', now()->subDays(7)))->count();
        $wonLeads        = $scope(Lead::where('status', 'won'))->count();
        $lostLeads       = $scope(Lead::where('status', 'lost'))->count();

        $totalClients    = $scope(Client::where('is_active', true))->count();
        $liveClients     = $scope(Client::where('is_live', true)->where('is_active', true))->count();

        // ── Revenue from Deals ──
        $totalDealValue      = $scope(Deal::whereNotIn('stage', ['closed_lost']))->sum('value');
        $wonDealValue        = $scope(Deal::where('stage', 'closed_won'))->sum('value');
        $pipelineValue       = $scope(Deal::whereNotIn('stage', ['closed_won', 'closed_lost']))->sum('value');
        $totalDeals          = $scope(Deal::query())->count();
        $openDeals           = $scope(Deal::whereNotIn('stage', ['closed_won', 'closed_lost']))->count();

        // ── Deal Pipeline by Stage ──
        $dealStages = [];
        foreach (Deal::KANBAN_STAGES as $stage) {
            $dealStages[$stage] = [
                'label' => Deal::STAGE_LABELS[$stage] ?? ucfirst($stage),
                'count' => $scope(Deal::where('stage', $stage))->count(),
                'value' => $scope(Deal::where('stage', $stage))->sum('value'),
                'color' => Deal::STAGE_COLORS[$stage] ?? '#94a3b8',
            ];
        }
        $closedWonCount   = $scope(Deal::where('stage', 'closed_won'))->count();
        $closedLostCount  = $scope(Deal::where('stage', 'closed_lost'))->count();

        // ── Support Tickets ──
        $openTickets       = $scope(SupportTicket::where('status', 'open'))->count();
        $inProgressTickets = $scope(SupportTicket::where('status', 'in_progress'))->count();
        $resolvedTickets   = $scope(SupportTicket::where('status', 'resolved'))->count();
        $closedTickets     = $scope(SupportTicket::where('status', 'closed'))->count();
        $totalTickets      = $scope(SupportTicket::query())->count();
        $urgentTickets     = $scope(SupportTicket::whereIn('status', ['open', 'in_progress'])->where('priority', 'urgent'))->count();
        $highTickets       = $scope(SupportTicket::whereIn('status', ['open', 'in_progress'])->where('priority', 'high'))->count();

        // ── Tasks ──
        $totalTasks     = $scope(Task::query())->count();
        $pendingTasks   = $scope(Task::where('status', 'pending'))->count();
        $inProgressTasks = $scope(Task::where('status', 'in_progress'))->count();
        $completedTasks = $scope(Task::where('status', 'completed'))->count();
        $taskProgress   = $totalTasks > 0 ? round(($completedTasks / $totalTasks) * 100) : 0;
        $overdueTasks   = $scope(Task::where('status', '!=', 'completed')->where('due_date', '<', today()))->count();

        // ── Conversion ──
        $convRate = $totalLeads > 0 ? round(($wonLeads / $totalLeads) * 100, 1) : 0;

        // ── Demo Requests & Messages ──
        $pendingDemos    = DemoRequest::where('status', 'pending')->count();
        $unreadMessages  = ContactMessage::where('status', 'new')->count();

        // ── Domain/Hosting Expiry Alerts (next 30 days) ──
        $expiringDomains = $scope(Client::where('is_active', true)
            ->whereNotNull('domain_expiry')
            ->where('domain_expiry', '<=', now()->addDays(30))
            ->where('domain_expiry', '>=', today()))
            ->orderBy('domain_expiry')
            ->get(['id', 'name', 'client_id', 'domain_name', 'domain_expiry']);

        $expiringHosting = $scope(Client::where('is_active', true)
            ->whereNotNull('hosting_expiry')
            ->where('hosting_expiry', '<=', now()->addDays(30))
            ->where('hosting_expiry', '>=', today()))
            ->orderBy('hosting_expiry')
            ->get(['id', 'name', 'client_id', 'hosting_provider', 'hosting_expiry']);

        // ── Recent Data ──
        $recentLeads    = $scope(Lead::query())->latest()->limit(5)->get();
        $recentTickets  = $scope(SupportTicket::with(['client', 'assignee']))->latest()->limit(5)->get();
        $recentDeals    = $scope(Deal::with(['lead', 'assignee']))->latest()->limit(5)->get();
        $upcomingTasks  = $scope(Task::where('status', '!=', 'completed'))->orderBy('due_date')->limit(5)->get();

        // ── Lead Sources Distribution ──
        $leadSources = $scope(Lead::query())->selectRaw('source, COUNT(*) as cnt')
            ->groupBy('source')
            ->pluck('cnt', 'source')
            ->toArray();

        // ── Lead Status Distribution ──
        $leadStatuses = $scope(Lead::query())->selectRaw('status, COUNT(*) as cnt')
            ->groupBy('status')
            ->pluck('cnt', 'status')
            ->toArray();

        // ── Current Team Info ──
        $currentTeam = $user?->currentTeam;

        return compact(
            'totalLeads', 'newLeadsToday', 'newLeadsWeek', 'wonLeads', 'lostLeads',
            'totalClients', 'liveClients',
            'totalDealValue', 'wonDealValue', 'pipelineValue', 'totalDeals', 'openDeals',
            'dealStages', 'closedWonCount', 'closedLostCount',
            'openTickets', 'inProgressTickets', 'resolvedTickets', 'closedTickets',
            'totalTickets', 'urgentTickets', 'highTickets',
            'totalTasks', 'pendingTasks', 'inProgressTasks', 'completedTasks',
            'taskProgress', 'overdueTasks',
            'convRate', 'pendingDemos', 'unreadMessages',
            'expiringDomains', 'expiringHosting',
            'recentLeads', 'recentTickets', 'recentDeals', 'upcomingTasks',
            'leadSources', 'leadStatuses', 'currentTeam'
        );
    }

    public function getWidgets(): array
    {
        return [];
    }
}
