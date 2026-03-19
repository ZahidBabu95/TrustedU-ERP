<?php

namespace App\Livewire;

use App\Models\AppNotification;
use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TopbarActions extends Component
{
    public ?int $selectedTeamId = null;

    public function mount(): void
    {
        $this->selectedTeamId = Auth::user()->current_team_id;
    }

    // ── Team Switching ──

    public function switchTeam(int $teamId): void
    {
        $user = Auth::user();
        $team = Team::find($teamId);

        if ($team && ($user->isAdmin() || $user->teams()->where('teams.id', $teamId)->exists())) {
            $user->update(['current_team_id' => $teamId]);
            $this->selectedTeamId = $teamId;
            $this->redirect(request()->header('Referer', '/admin'));
        }
    }

    public function clearTeam(): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $user->update(['current_team_id' => null]);
            $this->selectedTeamId = null;
            $this->redirect(request()->header('Referer', '/admin'));
        }
    }

    // ── Notifications ──

    public function markAsRead(string $id): void
    {
        $notification = AppNotification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->markAsRead();
        }
    }

    public function markAllRead(): void
    {
        AppNotification::markAllRead(Auth::id());
    }

    public function deleteNotification(string $id): void
    {
        $notification = AppNotification::find($id);
        if ($notification && $notification->user_id === Auth::id()) {
            $notification->delete();
        }
    }

    public function render()
    {
        $user = Auth::user();

        $teams = $user->isAdmin()
            ? Team::where('is_active', true)->get()
            : $user->teams()->where('is_active', true)->get();

        $notifications = AppNotification::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->limit(20)
            ->get();

        $unreadCount = AppNotification::unreadCount($user->id);

        return view('livewire.topbar-actions', [
            'teams'        => $teams,
            'currentTeam'  => $user->currentTeam,
            'isAdmin'      => $user->isAdmin(),
            'notifications' => $notifications,
            'unreadCount'  => $unreadCount,
        ]);
    }
}
