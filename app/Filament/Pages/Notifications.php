<?php

namespace App\Filament\Pages;

use App\Models\AppNotification;
use Filament\Pages\Page;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;

class Notifications extends Page
{
    protected string $view = 'filament.pages.notifications';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bell';
    protected static ?string $navigationLabel = 'Notifications';
    protected static ?string $title = 'Notifications';
    protected static ?string $slug = 'notifications';

    public static function getNavigationGroup(): ?string { return 'Platform'; }
    protected static ?int $navigationSort = 5;

    public static function getNavigationBadge(): ?string
    {
        $count = AppNotification::unreadCount(Auth::id() ?? 0);
        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    // State
    public string $activeTab = 'all'; // all, unread, read, settings
    public string $search = '';
    public string $typeFilter = '';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function markAsRead(string $id): void
    {
        $notification = AppNotification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
            Notification::make()->title('Marked as read')->success()->send();
        }
    }

    public function markAsUnread(string $id): void
    {
        $notification = AppNotification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->update(['read_at' => null]);
            Notification::make()->title('Marked as unread')->send();
        }
    }

    public function markAllRead(): void
    {
        $count = AppNotification::markAllRead(Auth::id());
        Notification::make()->title($count . ' notifications marked as read')->success()->send();
    }

    public function deleteNotification(string $id): void
    {
        $notification = AppNotification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->delete();
            Notification::make()->title('Notification deleted')->send();
        }
    }

    public function deleteAllRead(): void
    {
        $count = AppNotification::where('user_id', Auth::id())
            ->whereNotNull('read_at')
            ->delete();
        Notification::make()->title($count . ' read notifications cleared')->success()->send();
    }

    public function deleteAll(): void
    {
        $count = AppNotification::where('user_id', Auth::id())->delete();
        Notification::make()->title('All ' . $count . ' notifications deleted')->danger()->send();
    }

    public function getNotificationsProperty()
    {
        $query = AppNotification::where('user_id', Auth::id())
            ->orderByDesc('created_at');

        if ($this->activeTab === 'unread') {
            $query->whereNull('read_at');
        } elseif ($this->activeTab === 'read') {
            $query->whereNotNull('read_at');
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('message', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        return $query->paginate(15);
    }

    public function getStatsProperty(): array
    {
        $userId = Auth::id();
        return [
            'total'    => AppNotification::where('user_id', $userId)->count(),
            'unread'   => AppNotification::where('user_id', $userId)->whereNull('read_at')->count(),
            'read'     => AppNotification::where('user_id', $userId)->whereNotNull('read_at')->count(),
            'today'    => AppNotification::where('user_id', $userId)->whereDate('created_at', today())->count(),
        ];
    }

    public function getTypesProperty(): array
    {
        return AppNotification::where('user_id', Auth::id())
            ->selectRaw('type, COUNT(*) as count')
            ->groupBy('type')
            ->pluck('count', 'type')
            ->toArray();
    }
}
