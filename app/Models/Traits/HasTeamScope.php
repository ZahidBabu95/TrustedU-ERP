<?php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

/**
 * Add team-based data scoping to any model with a `team_id` column.
 *
 * Usage: `use HasTeamScope;` in any model, then:
 * - `Model::teamScoped()->get()` — returns only the current team's records
 * - `Model::forTeam($teamId)->get()` — returns a specific team's records
 */
trait HasTeamScope
{
    /**
     * Scope to current user's active team.
     * Admin/super_admin see all records (no filter applied).
     */
    public function scopeTeamScoped(Builder $query): Builder
    {
        $user = Auth::user();

        if (! $user) {
            return $query;
        }

        // Admins see everything
        if ($user->isAdmin()) {
            return $query;
        }

        // Team members only see their current team's data
        $teamId = $user->current_team_id;
        if ($teamId) {
            return $query->where($this->getTable() . '.team_id', $teamId);
        }

        // No team selected — show nothing for non-admins
        return $query->whereRaw('1 = 0');
    }

    /**
     * Scope to a specific team.
     */
    public function scopeForTeam(Builder $query, int $teamId): Builder
    {
        return $query->where($this->getTable() . '.team_id', $teamId);
    }

    /**
     * Relationship to team.
     */
    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class);
    }
}
