<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
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
        // New CRM fields
        'lead_id', 'deal_id',
        'billing_type', 'payment_frequency', 'package_price',
        'billing_status', 'activation_status', 'activation_date',
        'lead_support_person', 'secondary_support',
        'support_level', 'sla_response_hours',
        'implementation_status', 'implementation_progress',
        'client_priority', 'pipeline_stage',
    ];

    protected $casts = [
        'is_featured'       => 'boolean',
        'is_active'         => 'boolean',
        'is_live'           => 'boolean',
        'contract_start'    => 'date',
        'contract_end'      => 'date',
        'domain_expiry'     => 'date',
        'hosting_expiry'    => 'date',
        'activation_date'   => 'date',
        'package_price'     => 'decimal:2',
    ];

    protected $appends = ['logo_url'];

    // ── Pipeline Stages ──

    public const PIPELINE_MIGRATION  = 'migration';
    public const PIPELINE_TRAINING   = 'training';
    public const PIPELINE_BILLING    = 'billing_active';
    public const PIPELINE_ACTIVE     = 'active';

    public const PIPELINE_STAGE_LABELS = [
        'migration'      => '🔄 Migration',
        'training'       => '🎓 Training',
        'billing_active' => '💰 Billing Activation',
        'active'         => '✅ Active Client',
    ];

    public const PIPELINE_STAGE_COLORS = [
        'migration'      => '#f97316',
        'training'       => '#3b82f6',
        'billing_active' => '#8b5cf6',
        'active'         => '#22c55e',
    ];

    public const BILLING_TYPE_LABELS = [
        'prepaid'  => 'Prepaid',
        'postpaid' => 'Postpaid',
    ];

    public const PAYMENT_FREQUENCY_LABELS = [
        'monthly'     => 'Monthly',
        'quarterly'   => 'Quarterly',
        'half_yearly' => 'Half Yearly',
        'yearly'      => 'Yearly',
    ];

    public const BILLING_STATUS_LABELS = [
        'active'    => '🟢 Active',
        'suspended' => '🟠 Suspended',
        'overdue'   => '🔴 Overdue',
        'cancelled' => '⚫ Cancelled',
    ];

    public const ACTIVATION_STATUS_LABELS = [
        'pending'   => 'Pending',
        'active'    => 'Active',
        'suspended' => 'Suspended',
    ];

    public const SUPPORT_LEVEL_LABELS = [
        'basic'    => 'Basic',
        'standard' => 'Standard',
        'premium'  => 'Premium',
    ];

    public const IMPLEMENTATION_STATUS_LABELS = [
        'not_started'  => 'Not Started',
        'in_progress'  => 'In Progress',
        'completed'    => 'Completed',
    ];

    public const PRIORITY_LABELS = [
        'standard' => 'Standard',
        'priority' => 'Priority',
        'vip'      => 'VIP',
    ];

    // ── Boot ──

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

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function leadSupportUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'lead_support_person');
    }

    public function secondarySupportUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'secondary_support');
    }

    // ── CRM Relationships ──

    public function migration(): HasOne
    {
        return $this->hasOne(CrmMigration::class)->latestOfMany();
    }

    public function migrations(): HasMany
    {
        return $this->hasMany(CrmMigration::class);
    }

    public function trainings(): HasMany
    {
        return $this->hasMany(CrmTraining::class);
    }

    public function activeTraining(): HasOne
    {
        return $this->hasOne(CrmTraining::class)
            ->whereIn('status', ['scheduled', 'in_progress'])
            ->latestOfMany();
    }

    public function billingPlans(): HasMany
    {
        return $this->hasMany(CrmBillingPlan::class);
    }

    public function activeBillingPlan(): HasOne
    {
        return $this->hasOne(CrmBillingPlan::class)
            ->where('is_active', true)
            ->latestOfMany();
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(CrmInvoice::class)->orderByDesc('issue_date');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(CrmPayment::class)->orderByDesc('payment_date');
    }

    public function supportTickets(): HasMany
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function clientRequests(): HasMany
    {
        return $this->hasMany(CrmClientRequest::class);
    }

    public function activities()
    {
        return $this->hasMany(CrmActivity::class, 'entity_id')
            ->where('entity_type', 'client')
            ->orderByDesc('created_at');
    }

    public function followUps()
    {
        return $this->hasMany(CrmFollowUp::class, 'entity_id')
            ->where('entity_type', 'client')
            ->orderBy('scheduled_at');
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

    public function getTotalRevenueAttribute(): float
    {
        return $this->payments()->sum('amount');
    }

    public function getOpenTicketsCountAttribute(): int
    {
        return $this->supportTickets()->whereIn('status', ['open', 'in_progress'])->count();
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

    public function scopeByPipelineStage(Builder $query, string $stage): Builder
    {
        return $query->where('pipeline_stage', $stage);
    }
}
