<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'permissions',
        'avatar',
        'phone',
        'department',
        'designation',
        'current_team_id',
        'is_active',
        'last_login_at',
        'last_login_ip',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'last_login_at'     => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
            'permissions'       => 'array',
        ];
    }

    // ── All Available Permissions ──

    public const PERMISSIONS = [
        // Dashboard
        'dashboard.view'           => 'View Dashboard',

        // CRM
        'leads.view'               => 'View Leads',
        'leads.create'             => 'Create Leads',
        'leads.edit'               => 'Edit Leads',
        'leads.delete'             => 'Delete Leads',

        'deals.view'               => 'View Deals',
        'deals.create'             => 'Create Deals',
        'deals.edit'               => 'Edit Deals',
        'deals.delete'             => 'Delete Deals',

        'clients.view'             => 'View Clients',
        'clients.create'           => 'Create Clients',
        'clients.edit'             => 'Edit Clients',
        'clients.delete'           => 'Delete Clients',

        // Management
        'tasks.view'               => 'View Tasks',
        'tasks.create'             => 'Create Tasks',
        'tasks.edit'               => 'Edit Tasks',
        'tasks.delete'             => 'Delete Tasks',

        'support.view'             => 'View Support Tickets',
        'support.create'           => 'Create Tickets',
        'support.edit'             => 'Edit Tickets',
        'support.delete'           => 'Delete Tickets',
        'support.reply'            => 'Reply to Tickets',

        'team.view'                => 'View Team Members',
        'team.create'              => 'Create Team Members',
        'team.edit'                => 'Edit Team Members',
        'team.delete'              => 'Delete Team Members',

        // Website CMS
        'cms.blog'                 => 'Manage Blog Posts',
        'cms.pages'                => 'Manage Pages',
        'cms.settings'             => 'Website Settings',
        'cms.contacts'             => 'View Contact Messages',
        'cms.demos'                => 'View Demo Requests',

        // Platform
        'users.view'               => 'View Users',
        'users.create'             => 'Create Users',
        'users.edit'               => 'Edit Users',
        'users.delete'             => 'Delete Users',
        'users.impersonate'        => 'Login As User',

        'settings.view'            => 'View System Settings',
        'settings.edit'            => 'Edit System Settings',

        'reports.view'             => 'View Reports',
        'notifications.manage'     => 'Manage Notifications',
    ];

    public const PERMISSION_GROUPS = [
        'Dashboard'   => ['dashboard.view'],
        'CRM - Leads' => ['leads.view', 'leads.create', 'leads.edit', 'leads.delete'],
        'CRM - Deals' => ['deals.view', 'deals.create', 'deals.edit', 'deals.delete'],
        'CRM - Clients' => ['clients.view', 'clients.create', 'clients.edit', 'clients.delete'],
        'Tasks'       => ['tasks.view', 'tasks.create', 'tasks.edit', 'tasks.delete'],
        'Support'     => ['support.view', 'support.create', 'support.edit', 'support.delete', 'support.reply'],
        'Team'        => ['team.view', 'team.create', 'team.edit', 'team.delete'],
        'Website CMS' => ['cms.blog', 'cms.pages', 'cms.settings', 'cms.contacts', 'cms.demos'],
        'Users'       => ['users.view', 'users.create', 'users.edit', 'users.delete', 'users.impersonate'],
        'Settings'    => ['settings.view', 'settings.edit'],
        'Reports'     => ['reports.view'],
        'Notifications' => ['notifications.manage'],
    ];

    // ── Relationships ──

    public function profile(): HasOne
    {
        return $this->hasOne(EmployeeProfile::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(EmployeeDocument::class);
    }

    public function financial(): HasOne
    {
        return $this->hasOne(EmployeeFinancial::class);
    }

    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'team_user')
            ->withPivot('role_in_team', 'joined_at')
            ->withTimestamps();
    }

    public function currentTeam(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'current_team_id');
    }

    // ── Helpers ──

    public function canAccessPanel(Panel $panel): bool
    {
        return $this->is_active && in_array($this->role, [
            'super_admin', 'admin', 'editor', 'sales', 'team_member',
        ]);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'super_admin';
    }

    public function isAdmin(): bool
    {
        return in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * Check if user has a specific permission.
     * Super admins always have all permissions.
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->isSuperAdmin()) return true;
        if ($this->isAdmin()) return true; // Admins have all by default
        return in_array($permission, $this->permissions ?? []);
    }

    /**
     * Check if user has any of the given permissions.
     */
    public function hasAnyPermission(array $permissions): bool
    {
        foreach ($permissions as $permission) {
            if ($this->hasPermission($permission)) return true;
        }
        return false;
    }

    /**
     * Grant permissions to user.
     */
    public function grantPermissions(array $permissions): void
    {
        $current = $this->permissions ?? [];
        $this->update(['permissions' => array_unique(array_merge($current, $permissions))]);
    }

    /**
     * Revoke permissions from user.
     */
    public function revokePermissions(array $permissions): void
    {
        $current = $this->permissions ?? [];
        $this->update(['permissions' => array_values(array_diff($current, $permissions))]);
    }

    /**
     * Set all permissions at once.
     */
    public function setPermissions(array $permissions): void
    {
        $this->update(['permissions' => array_values($permissions)]);
    }

    /**
     * Switch to a different team.
     */
    public function switchTeam(Team $team): void
    {
        if ($this->teams()->where('teams.id', $team->id)->exists()) {
            $this->update(['current_team_id' => $team->id]);
        }
    }

    /**
     * Get the current team ID for scoping data.
     */
    public function getTeamIdForScoping(): ?int
    {
        if ($this->isAdmin()) {
            return null;
        }
        return $this->current_team_id;
    }

    public function getFilamentAvatarUrl(): ?string
    {
        if ($this->profile?->profile_photo) {
            return Storage::url($this->profile->profile_photo);
        }
        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    public function getInitialsAttribute(): string
    {
        return collect(explode(' ', $this->name))
            ->map(fn($w) => strtoupper($w[0] ?? ''))
            ->take(2)
            ->implode('');
    }

    /**
     * Record login info.
     */
    public function recordLogin(): void
    {
        $this->update([
            'last_login_at' => now(),
            'last_login_ip' => request()->ip(),
        ]);
    }
}
