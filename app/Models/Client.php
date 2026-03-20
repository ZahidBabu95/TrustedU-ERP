<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Client extends Model
{
    protected $fillable = [
        'client_id', 'name', 'logo', 'logo_disk', 'website',
        'domain_name', 'domain_expiry', 'domain_provider',
        'hosting_provider', 'hosting_package', 'hosting_expiry', 'hosting_notes',
        'email', 'phone', 'address',
        'institution_type', 'district',
        'principal_name', 'principal_phone',
        'contract_start', 'contract_end',
        'is_featured', 'is_active', 'is_live', 'sort_order',
    ];

    protected $casts = [
        'is_featured'    => 'boolean',
        'is_active'      => 'boolean',
        'is_live'        => 'boolean',
        'contract_start' => 'date',
        'contract_end'   => 'date',
        'domain_expiry'  => 'date',
        'hosting_expiry' => 'date',
    ];

    protected $appends = ['logo_url'];

    protected static function booted(): void
    {
        static::creating(function (Client $client) {
            if (empty($client->client_id)) {
                $lastId = static::max('id') ?? 0;
                $client->client_id = str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
            }
        });
    }

    // ── Relationships ──

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'client_team')
            ->withTimestamps();
    }

    public function erpModules(): BelongsToMany
    {
        return $this->belongsToMany(ErpModule::class, 'client_erp_module')
            ->withPivot(['activated_at', 'is_active'])
            ->withTimestamps();
    }

    public function monthlyStats(): HasMany
    {
        return $this->hasMany(ClientMonthlyStat::class)
            ->orderByDesc('year')
            ->orderByDesc('month');
    }

    // ── Accessors ──

    public function getLogoUrlAttribute(): ?string
    {
        if (empty($this->logo)) {
            return null;
        }

        $disk = $this->logo_disk ?? 'public';

        try {
            return Storage::disk($disk)->url($this->logo);
        } catch (\Exception $e) {
            return Storage::disk('public')->url($this->logo);
        }
    }

    public function getActiveModulesCountAttribute(): int
    {
        return $this->erpModules()->wherePivot('is_active', true)->count();
    }

    public function getLatestStudentCountAttribute(): ?int
    {
        $latest = $this->monthlyStats()->first();
        return $latest?->active_students;
    }

    // ── Scopes ──

    public function scopeTeamScoped(Builder $query): Builder
    {
        $user = Auth::user();
        if (!$user) return $query;
        if ($user->isAdmin()) return $query;

        $teamId = $user->current_team_id;
        if ($teamId) {
            return $query->whereHas('teams', fn (Builder $q) =>
                $q->where('teams.id', $teamId)
            );
        }
        return $query->whereRaw('1 = 0');
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeLive(Builder $query): Builder
    {
        return $query->where('is_live', true)->where('is_active', true);
    }

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
