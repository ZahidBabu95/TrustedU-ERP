<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CrmMigration extends Model
{
    protected $guarded = [];

    protected $casts = [
        'data_categories'      => 'array',
        'onboarding_plan'      => 'array',
        'checklist_items'      => 'array',
        'training_data'        => 'array',
        'handover_data'        => 'array',
        'invoice_data'         => 'array',
        'migration_start_date' => 'date',
        'migration_end_date'   => 'date',
        'actual_end_date'      => 'date',
        'decommission_date'    => 'date',
        'signoff_at'           => 'datetime',
    ];

    // ── 7-Step Pipeline ──

    public const STEP_ONBOARDING_PLAN    = 'onboarding_plan';
    public const STEP_DATA_PROCESSING    = 'data_processing';
    public const STEP_SYSTEM_ENTRY       = 'system_entry';
    public const STEP_ONBOARDING_CHECK   = 'onboarding_checklist';
    public const STEP_TRAINING           = 'training';
    public const STEP_HANDOVER           = 'handover';
    public const STEP_INVOICE_DEED       = 'invoice_deed';
    public const STEP_COMPLETED          = 'completed';

    public const PIPELINE_STEPS = [
        self::STEP_ONBOARDING_PLAN,
        self::STEP_DATA_PROCESSING,
        self::STEP_SYSTEM_ENTRY,
        self::STEP_ONBOARDING_CHECK,
        self::STEP_TRAINING,
        self::STEP_HANDOVER,
        self::STEP_INVOICE_DEED,
        self::STEP_COMPLETED,
    ];

    public const STEP_LABELS = [
        'onboarding_plan'     => '📋 Onboarding Plan',
        'data_processing'     => '⚙️ Data Processing',
        'system_entry'        => '💻 System Entry',
        'onboarding_checklist'=> '✅ Onboarding Checklist',
        'training'            => '🎓 Training',
        'handover'            => '🤝 Handover',
        'invoice_deed'        => '📄 Invoice & Deed',
        'completed'           => '🏆 Completed',
    ];

    public const STEP_ICONS = [
        'onboarding_plan'     => '📋',
        'data_processing'     => '⚙️',
        'system_entry'        => '💻',
        'onboarding_checklist'=> '✅',
        'training'            => '🎓',
        'handover'            => '🤝',
        'invoice_deed'        => '📄',
        'completed'           => '🏆',
    ];

    // ── Status Constants ──

    public const STATUS_NOT_STARTED      = 'not_started';
    public const STATUS_IN_PROGRESS      = 'in_progress';
    public const STATUS_COMPLETED        = 'completed';
    public const STATUS_FAILED           = 'failed';

    public const STATUS_LABELS = [
        'not_started'  => 'Not Started',
        'in_progress'  => 'In Progress',
        'completed'    => 'Completed',
        'failed'       => 'Failed',
    ];

    public const STATUS_COLORS = [
        'not_started'  => '#94a3b8',
        'in_progress'  => '#3b82f6',
        'completed'    => '#22c55e',
        'failed'       => '#ef4444',
    ];

    public const DATA_TYPE_LABELS = [
        'hard_copy'    => '📄 Hard Copy',
        'soft_copy'    => '💾 Soft Copy (Excel/CSV)',
        'database'     => '🗄️ Database Export',
        'api'          => '🔗 API',
        'mixed'        => '📦 Mixed (Hard + Soft)',
        'no_data'      => '🆕 No Previous Data',
    ];

    public const OLD_SYSTEM_STATUS_LABELS = [
        'running'          => '🟢 Running',
        'parallel_run'     => '🟡 Parallel Run',
        'suspended'        => '🟠 Suspended',
        'decommissioned'   => '🔴 Decommissioned',
    ];

    public const COLLECTION_METHOD_LABELS = [
        'export_file'   => 'Export File (CSV/Excel)',
        'api'           => 'API Integration',
        'manual'        => 'Manual Entry',
        'database_dump' => 'Database Dump',
    ];

    // ── Default Onboarding Checklist ──

    public const DEFAULT_CHECKLIST = [
        ['item' => 'প্রতিষ্ঠানের তথ্য সংগ্রহ সম্পন্ন', 'done' => false],
        ['item' => 'ডেটা ফরম্যাট যাচাই ও কনভার্ট সম্পন্ন', 'done' => false],
        ['item' => 'ছাত্র/ছাত্রী ডেটা এন্ট্রি সম্পন্ন', 'done' => false],
        ['item' => 'শিক্ষক/কর্মী ডেটা এন্ট্রি সম্পন্ন', 'done' => false],
        ['item' => 'ফি কাঠামো সেটআপ সম্পন্ন', 'done' => false],
        ['item' => 'পরীক্ষা/রেজাল্ট কনফিগারেশন সম্পন্ন', 'done' => false],
        ['item' => 'SMS/কমিউনিকেশন সেটআপ সম্পন্ন', 'done' => false],
        ['item' => 'ইউজার অ্যাকাউন্ট ও রোল সেটআপ সম্পন্ন', 'done' => false],
        ['item' => 'ডেটা ভেরিফিকেশন ও QA সম্পন্ন', 'done' => false],
        ['item' => 'ক্লায়েন্ট দ্বারা ডেটা অনুমোদন সম্পন্ন', 'done' => false],
    ];

    // ── Relationships ──

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function deal(): BelongsTo
    {
        return $this->belongsTo(Deal::class);
    }

    public function assignee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function signoffUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'signoff_by');
    }

    public function tasks(): HasMany
    {
        return $this->hasMany(CrmMigrationTask::class, 'migration_id')->orderBy('sort_order');
    }

    // ── Pipeline Helpers ──

    public function getCurrentStepIndex(): int
    {
        return array_search($this->current_step, self::PIPELINE_STEPS) ?: 0;
    }

    public function getStepProgress(): array
    {
        $currentIdx = $this->getCurrentStepIndex();
        $steps = [];
        foreach (self::PIPELINE_STEPS as $i => $step) {
            $steps[] = [
                'key'   => $step,
                'label' => self::STEP_LABELS[$step] ?? $step,
                'icon'  => self::STEP_ICONS[$step] ?? '📌',
                'done'  => $i < $currentIdx,
                'current' => $i === $currentIdx,
            ];
        }
        return $steps;
    }

    public function advanceStep(): ?string
    {
        $steps = self::PIPELINE_STEPS;
        $currentIdx = array_search($this->current_step, $steps);
        if ($currentIdx === false || $currentIdx >= count($steps) - 1) return null;
        $next = $steps[$currentIdx + 1];
        $this->update([
            'current_step' => $next,
            'status' => $next === 'completed' ? 'completed' : 'in_progress',
            'progress_percent' => (int) round((($currentIdx + 1) / (count($steps) - 1)) * 100),
        ]);
        if ($next === 'completed') {
            $this->update(['actual_end_date' => now()]);
        }
        return $next;
    }

    // ── Legacy Helpers ──

    public function generateDefaultTasks(): void
    {
        $defaultTasks = [
            ['task_category' => 'onboarding',      'title' => 'প্রতিষ্ঠান থেকে ডেটা সংগ্রহ পদ্ধতি নির্ধারণ',     'priority' => 'critical', 'sort_order' => 1],
            ['task_category' => 'onboarding',      'title' => 'ডেটা ফরম্যাট (হার্ড/সফট কপি) নথিভুক্ত',          'priority' => 'critical', 'sort_order' => 2],
            ['task_category' => 'data_processing', 'title' => 'সংগৃহীত ডেটা প্রসেস ও ক্লিনিং',                   'priority' => 'critical', 'sort_order' => 3],
            ['task_category' => 'data_processing', 'title' => 'ডেটা ফরম্যাট কনভার্সন (Excel/CSV)',              'priority' => 'high',     'sort_order' => 4],
            ['task_category' => 'system_entry',    'title' => 'ছাত্র/ছাত্রী ডেটা সিস্টেমে এন্ট্রি',                'priority' => 'critical', 'sort_order' => 5],
            ['task_category' => 'system_entry',    'title' => 'শিক্ষক/কর্মী ডেটা সিস্টেমে এন্ট্রি',               'priority' => 'high',     'sort_order' => 6],
            ['task_category' => 'system_entry',    'title' => 'ফি কাঠামো ও আর্থিক ডেটা এন্ট্রি',                'priority' => 'high',     'sort_order' => 7],
            ['task_category' => 'verification',    'title' => 'এন্ট্রি করা ডেটা যাচাই (স্যাম্পল চেক)',             'priority' => 'critical', 'sort_order' => 8],
            ['task_category' => 'training',        'title' => 'অ্যাডমিন প্যানেল ট্রেনিং',                         'priority' => 'critical', 'sort_order' => 9],
            ['task_category' => 'training',        'title' => 'শিক্ষক/কর্মী ট্রেনিং',                              'priority' => 'high',     'sort_order' => 10],
            ['task_category' => 'handover',        'title' => 'সিস্টেম হ্যান্ডওভার ও ক্রেডেনশিয়াল প্রদান',       'priority' => 'critical', 'sort_order' => 11],
            ['task_category' => 'invoice',         'title' => 'ইনভয়েস তৈরি ও পাঠানো',                           'priority' => 'critical', 'sort_order' => 12],
            ['task_category' => 'invoice',         'title' => 'ডীড/চুক্তিপত্র চূড়ান্ত',                            'priority' => 'critical', 'sort_order' => 13],
        ];

        foreach ($defaultTasks as $task) {
            $this->tasks()->create(array_merge($task, [
                'status' => 'pending',
                'assigned_to' => $this->assigned_to,
            ]));
        }
    }

    public function updateProgress(): void
    {
        $total     = $this->tasks()->count();
        $completed = $this->tasks()->where('status', 'completed')->count();

        $this->update([
            'progress_percent' => $total > 0 ? (int) round(($completed / $total) * 100) : 0,
        ]);
    }

    public function canComplete(): bool
    {
        return $this->tasks()
            ->where('priority', 'critical')
            ->where('status', '!=', 'completed')
            ->doesntExist();
    }

    // ── Scopes ──

    public function scopeActive($query)
    {
        return $query->whereNotIn('status', ['completed', 'failed']);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeTrial($query)
    {
        return $query->where('current_step', '!=', 'completed')
                     ->where('status', '!=', 'failed');
    }
}
