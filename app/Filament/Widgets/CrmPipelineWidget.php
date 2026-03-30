<?php

namespace App\Filament\Widgets;

use App\Models\Client;
use App\Models\CrmBillingPlan;
use App\Models\CrmFollowUp;
use App\Models\CrmInvoice;
use App\Models\CrmMigration;
use App\Models\CrmPayment;
use App\Models\Deal;
use App\Models\Lead;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CrmPipelineWidget extends BaseWidget
{
    protected ?string $pollingInterval = '60s';
    protected static ?int $sort = -1;
    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $hotLeads = Lead::where('interest_level', 'hot')
            ->whereNotIn('status', ['won', 'lost'])->count();
        $activeLeads = Lead::whereNotIn('status', ['won', 'lost'])->count();
        $qualifiedLeads = Lead::where('pipeline_stage', 'qualified')->count();

        $activeDeals = Deal::whereNotIn('stage', ['closed_won', 'closed_lost'])->count();
        $dealValue = Deal::whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value');
        $wonDeals = Deal::where('stage', 'closed_won')->count();
        $wonValue = Deal::where('stage', 'closed_won')->sum('value');

        $activeClients = Client::where('is_active', true)->count();
        $liveClients = Client::where('is_live', true)->where('is_active', true)->count();

        $overdueFollowUps = CrmFollowUp::overdue()->count();
        $todayFollowUps = CrmFollowUp::today()->pending()->count();

        $activeMigrations = CrmMigration::active()->count();

        // Billing stats
        $activePlans = CrmBillingPlan::active()->count();
        $overdueInvoices = CrmInvoice::overdue()->count();
        $monthlyRevenue = CrmPayment::thisMonth()->sum('amount');

        return [
            Stat::make('🔥 Hot Leads', $hotLeads)
                ->description("{$activeLeads} total active leads")
                ->descriptionIcon('heroicon-o-fire')
                ->color('danger'),

            Stat::make('✅ Qualified', $qualifiedLeads)
                ->description('Ready for conversion')
                ->descriptionIcon('heroicon-o-check-badge')
                ->color('success'),

            Stat::make('💼 Active Deals', $activeDeals)
                ->description('৳' . number_format($dealValue) . ' pipeline value')
                ->descriptionIcon('heroicon-o-briefcase')
                ->color('primary'),

            Stat::make('🏆 Won Deals', $wonDeals)
                ->description('৳' . number_format($wonValue) . ' total revenue')
                ->descriptionIcon('heroicon-o-trophy')
                ->color('success'),

            Stat::make('🏢 Active Clients', $activeClients)
                ->description("{$liveClients} live clients")
                ->descriptionIcon('heroicon-o-building-office-2')
                ->color('info'),

            Stat::make('📅 Follow-ups', $todayFollowUps)
                ->description($overdueFollowUps > 0 ? "⚠️ {$overdueFollowUps} overdue!" : 'All up to date')
                ->descriptionIcon($overdueFollowUps > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->color($overdueFollowUps > 0 ? 'danger' : 'success'),

            Stat::make('💳 Billing Plans', $activePlans)
                ->description($overdueInvoices > 0 ? "⚠️ {$overdueInvoices} overdue invoices" : 'No overdue invoices')
                ->descriptionIcon($overdueInvoices > 0 ? 'heroicon-o-exclamation-triangle' : 'heroicon-o-check-circle')
                ->color($overdueInvoices > 0 ? 'danger' : 'success'),

            Stat::make('💰 Monthly Revenue', '৳' . number_format($monthlyRevenue))
                ->description('This month payments')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('primary'),
        ];
    }
}
