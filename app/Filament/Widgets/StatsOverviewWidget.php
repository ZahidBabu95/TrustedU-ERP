<?php

namespace App\Filament\Widgets;

use App\Models\DemoRequest;
use App\Models\ContactMessage;
use App\Models\Client;
use App\Models\BlogPost;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class StatsOverviewWidget extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Demo Requests', DemoRequest::count())
                ->description(DemoRequest::where('status', 'pending')->count() . ' pending')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('warning')
                ->chart(
                    DemoRequest::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                        ->groupBy('date')->orderBy('date')->take(7)
                        ->pluck('count')->toArray()
                ),

            Stat::make('Unread Messages', ContactMessage::where('status', 'new')->count())
                ->description('Total: ' . ContactMessage::count())
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger'),

            Stat::make('Total Clients', Client::where('is_active', true)->count())
                ->description('Active institutions')
                ->descriptionIcon('heroicon-m-building-office')
                ->color('success'),

            Stat::make('Blog Posts', BlogPost::where('status', 'published')->count())
                ->description(BlogPost::where('status', 'draft')->count() . ' drafts')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info'),
        ];
    }
}
