<?php

namespace App\Livewire;

use App\Models\Team;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class TeamSwitcher extends Component
{
    public ?int $selectedTeamId = null;

    public function mount(): void
    {
        $this->selectedTeamId = Auth::user()->current_team_id;
    }

    public function switchTeam(int $teamId): void
    {
        $user = Auth::user();
        $team = Team::find($teamId);

        if ($team && $user->teams()->where('teams.id', $teamId)->exists()) {
            $user->update(['current_team_id' => $teamId]);
            $this->selectedTeamId = $teamId;
            $this->dispatch('team-switched');
            $this->redirect(request()->header('Referer', '/admin'));
        }
    }

    public function clearTeam(): void
    {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $user->update(['current_team_id' => null]);
            $this->selectedTeamId = null;
            $this->dispatch('team-switched');
            $this->redirect(request()->header('Referer', '/admin'));
        }
    }

    public function render()
    {
        $user = Auth::user();
        $teams = $user->isAdmin()
            ? Team::where('is_active', true)->get()
            : $user->teams()->where('is_active', true)->get();

        return view('livewire.team-switcher', [
            'teams' => $teams,
            'currentTeam' => $user->currentTeam,
            'isAdmin' => $user->isAdmin(),
        ]);
    }
}
