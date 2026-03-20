<?php

namespace App\Filament\Pages;

use App\Models\ContactMessage;
use App\Models\DemoRequest;
use App\Models\ErpModule;
use App\Models\Setting;
use Carbon\Carbon;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class WebsiteDashboard extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';
    protected static ?string $navigationLabel = 'Website Dashboard';
    protected static ?string $title = 'Website Dashboard';
    protected static ?string $slug = 'website-dashboard';

    public static function getNavigationGroup(): ?string { return 'Website CMS'; }
    protected static ?int $navigationSort = 0;

    public function getView(): string
    {
        return 'filament.pages.website-dashboard';
    }

    public function getViewData(): array
    {
        // ── Demo Requests Analytics ──
        $totalDemos       = DemoRequest::count();
        $pendingDemos     = DemoRequest::where('status', 'pending')->count();
        $approvedDemos    = DemoRequest::where('status', 'approved')->count();
        $completedDemos   = DemoRequest::where('status', 'completed')->count();
        $rejectedDemos    = DemoRequest::where('status', 'rejected')->count();
        $todayDemos       = DemoRequest::whereDate('created_at', today())->count();
        $weekDemos        = DemoRequest::where('created_at', '>=', now()->subDays(7))->count();
        $monthDemos       = DemoRequest::where('created_at', '>=', now()->subDays(30))->count();

        // Demo requests trend (last 14 days)
        $demoTrend = DemoRequest::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as cnt')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('cnt', 'date')
            ->toArray();

        // Fill in missing dates
        $demoTrendFull = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $demoTrendFull[$d] = $demoTrend[$d] ?? 0;
        }

        // Demo sources distribution
        $demoSources = DemoRequest::selectRaw('COALESCE(source, "Direct") as source, COUNT(*) as cnt')
            ->groupBy('source')
            ->pluck('cnt', 'source')
            ->toArray();

        // Institution types from demo requests
        $institutionTypes = DemoRequest::selectRaw('COALESCE(institution_type, "Other") as itype, COUNT(*) as cnt')
            ->groupBy('itype')
            ->pluck('cnt', 'itype')
            ->toArray();

        // ── Contact Messages Analytics ──
        $totalMessages    = ContactMessage::count();
        $unreadMessages   = ContactMessage::where('status', 'new')->count();
        $readMessages     = ContactMessage::where('status', 'read')->count();
        $todayMessages    = ContactMessage::whereDate('created_at', today())->count();
        $weekMessages     = ContactMessage::where('created_at', '>=', now()->subDays(7))->count();

        // Messages trend (last 14 days)
        $messageTrend = ContactMessage::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as cnt')
            )
            ->where('created_at', '>=', now()->subDays(14))
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('cnt', 'date')
            ->toArray();

        $messageTrendFull = [];
        for ($i = 13; $i >= 0; $i--) {
            $d = now()->subDays($i)->format('Y-m-d');
            $messageTrendFull[$d] = $messageTrend[$d] ?? 0;
        }

        // ── ERP Modules Analytics ──
        $totalModules     = ErpModule::count();
        $activeModules    = ErpModule::where('is_active', true)->count();
        $modulesWithVideo = ErpModule::where('is_active', true)
            ->whereNotNull('youtube_videos')
            ->get()
            ->filter(fn($m) => is_array($m->youtube_videos) && count($m->youtube_videos) > 0)
            ->count();
        $totalVideos = ErpModule::where('is_active', true)
            ->whereNotNull('youtube_videos')
            ->get()
            ->sum(fn($m) => is_array($m->youtube_videos) ? count($m->youtube_videos) : 0);

        // ── Most Requested Modules (from demo requests) ──
        $modulePopularity = [];
        $demoWithModules = DemoRequest::whereNotNull('interested_modules')->get();
        foreach ($demoWithModules as $demo) {
            if (is_array($demo->interested_modules)) {
                foreach ($demo->interested_modules as $mod) {
                    $modulePopularity[$mod] = ($modulePopularity[$mod] ?? 0) + 1;
                }
            }
        }
        arsort($modulePopularity);
        $modulePopularity = array_slice($modulePopularity, 0, 10, true);

        // ── Recent Demo Requests ──
        $recentDemos = DemoRequest::latest()->limit(8)->get();

        // ── Recent Messages ──
        $recentMessages = ContactMessage::latest()->limit(8)->get();

        // ── Conversion Rate (pending → approved/completed) ──
        $demoConvRate = $totalDemos > 0
            ? round((($approvedDemos + $completedDemos) / $totalDemos) * 100, 1)
            : 0;

        // ── Google Analytics Status ──
        $gaEnabled       = \App\Models\SystemSetting::get('ga_enabled', false);
        $gaMeasurementId = \App\Models\SystemSetting::get('ga_measurement_id');

        return compact(
            'totalDemos', 'pendingDemos', 'approvedDemos', 'completedDemos', 'rejectedDemos',
            'todayDemos', 'weekDemos', 'monthDemos', 'demoTrendFull', 'demoSources', 'institutionTypes',
            'totalMessages', 'unreadMessages', 'readMessages', 'todayMessages', 'weekMessages',
            'messageTrendFull',
            'totalModules', 'activeModules', 'modulesWithVideo', 'totalVideos', 'modulePopularity',
            'recentDemos', 'recentMessages',
            'demoConvRate', 'gaEnabled', 'gaMeasurementId'
        );
    }

    public static function canAccess(): bool
    {
        return Auth::user()?->isAdmin() ?? false;
    }
}
