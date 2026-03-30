<?php

namespace App\Filament\Resources\CrmMigrations\Pages;

use App\Filament\Resources\CrmMigrations\CrmMigrationResource;
use App\Models\CrmActivity;
use App\Models\CrmMigration;
use App\Models\CrmMigrationTask;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Livewire\WithFileUploads;

class ViewCrmMigration extends ViewRecord
{
    use WithFileUploads;

    protected static string $resource = CrmMigrationResource::class;
    protected string $view = 'filament.resources.crm-migrations.pages.view-migration';

    // ── Livewire Properties ──
    public string $viewingStep = '';
    public array $taskForm = [];
    public array $onboardingPlan = [];
    public $taskFile = null;

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $migration = $this->record;
        $plan = $migration->onboarding_plan ?? [];

        $this->onboardingPlan = [
            'data_type'          => $plan['data_type'] ?? '',
            'data_source'        => $plan['data_source'] ?? '',
            'conversion_plan'    => $plan['conversion_plan'] ?? '',
            'estimated_records'  => $plan['estimated_records'] ?? '',
            'contact_person'     => $plan['contact_person'] ?? '',
            'contact_phone'      => $plan['contact_phone'] ?? '',
            'special_notes'      => $plan['special_notes'] ?? '',
        ];

        $this->taskForm = [
            'title'         => '',
            'description'   => '',
            'task_category' => $migration->current_step === 'onboarding_plan' ? 'onboarding' : ($migration->current_step === 'data_processing' ? 'data_processing' : 'system_entry'),
            'priority'      => 'medium',
            'due_date'      => '',
            'notes'         => '',
        ];
    }

    // ── View a completed step (without changing the current step) ──
    public function viewStep(string $step): void
    {
        $migration = $this->record;
        $steps = CrmMigration::PIPELINE_STEPS;
        $currentIdx = array_search($migration->current_step, $steps);
        $targetIdx  = array_search($step, $steps);

        if ($targetIdx !== false && ($targetIdx < $currentIdx || $step === $migration->current_step)) {
            $this->viewingStep = $step;
        }
    }

    public function clearViewingStep(): void
    {
        $this->viewingStep = '';
    }

    // ── Save Onboarding Plan ──
    public function saveOnboardingPlan(): void
    {
        $migration = $this->record;

        if (empty($this->onboardingPlan['data_type'])) {
            Notification::make()->title('Data type is required.')->danger()->send();
            return;
        }

        $migration->update([
            'onboarding_plan' => $this->onboardingPlan,
        ]);

        CrmActivity::log('migration', $migration->id, 'note',
            '📋 Onboarding plan saved',
            'Data Type: ' . (CrmMigration::DATA_TYPE_LABELS[$this->onboardingPlan['data_type']] ?? $this->onboardingPlan['data_type']),
            $this->onboardingPlan
        );

        Notification::make()->title('📋 Onboarding Plan Saved!')->success()->send();
    }

    // ── Add Task ──
    public function addTask(): void
    {
        $migration = $this->record;

        if (empty($this->taskForm['title'])) {
            Notification::make()->title('Task title is required.')->danger()->send();
            return;
        }

        $maxSort = $migration->tasks()->max('sort_order') ?? 0;

        $taskData = [
            'title'         => $this->taskForm['title'],
            'description'   => $this->taskForm['description'] ?: null,
            'task_category' => $this->taskForm['task_category'],
            'priority'      => $this->taskForm['priority'],
            'status'        => 'pending',
            'due_date'      => !empty($this->taskForm['due_date']) ? $this->taskForm['due_date'] : null,
            'notes'         => $this->taskForm['notes'] ?: null,
            'assigned_to'   => $migration->assigned_to,
            'sort_order'    => $maxSort + 1,
        ];

        // Handle file upload
        if ($this->taskFile) {
            $path = $this->taskFile->store('migration-files/' . $migration->id, 'public');
            $taskData['file_path'] = $path;
            $taskData['file_disk'] = 'public';
        }

        $migration->tasks()->create($taskData);

        CrmActivity::log('migration', $migration->id, 'task_create',
            "Task added: {$this->taskForm['title']}",
            "Category: " . (CrmMigrationTask::CATEGORY_LABELS[$this->taskForm['task_category']] ?? $this->taskForm['task_category']),
            $this->taskForm
        );

        Notification::make()->title('✅ Task Added!')->success()->send();

        // Reset form
        $this->taskForm = [
            'title' => '', 'description' => '', 'task_category' => $this->taskForm['task_category'],
            'priority' => 'medium', 'due_date' => '', 'notes' => '',
        ];
        $this->taskFile = null;

        $this->redirect(CrmMigrationResource::getUrl('view', ['record' => $migration]));
    }

    // ── Complete a Task ──
    public function completeTask(int $taskId): void
    {
        $task = CrmMigrationTask::find($taskId);
        if ($task && $task->migration_id === $this->record->id) {
            $task->markCompleted();
            $this->record->refresh();
            Notification::make()->title("✅ Task completed: {$task->title}")->success()->send();
            $this->redirect(CrmMigrationResource::getUrl('view', ['record' => $this->record]));
        }
    }

    // ── Toggle Checklist Item ──
    public function toggleChecklist(int $index): void
    {
        $migration = $this->record;
        $items = $migration->checklist_items ?? [];

        if (isset($items[$index])) {
            $items[$index]['done'] = !$items[$index]['done'];
            $migration->update(['checklist_items' => $items]);
            $this->record->refresh();
        }
    }

    // ── Save Training Data ──
    public array $trainingForm = [];

    public function saveTrainingData(): void
    {
        $migration = $this->record;
        $migration->update(['training_data' => $this->trainingForm]);

        CrmActivity::log('migration', $migration->id, 'note',
            '🎓 Training data saved',
            null,
            $this->trainingForm
        );

        Notification::make()->title('🎓 Training Data Saved!')->success()->send();
    }

    // ── Save Handover Data ──
    public array $handoverForm = [];

    public function saveHandoverData(): void
    {
        $migration = $this->record;
        $migration->update(['handover_data' => $this->handoverForm]);

        CrmActivity::log('migration', $migration->id, 'note',
            '🤝 Handover data saved',
            null,
            $this->handoverForm
        );

        Notification::make()->title('🤝 Handover Data Saved!')->success()->send();
    }

    // ── Advance Step ──
    public function advanceStep(): void
    {
        $migration = $this->record;

        $newStep = $migration->advanceStep();
        if (!$newStep) {
            Notification::make()->title('Cannot advance further.')->warning()->send();
            return;
        }

        CrmActivity::log('migration', $migration->id, 'stage_change',
            'Migration Step Advanced → ' . (CrmMigration::STEP_LABELS[$newStep] ?? $newStep),
            'Progress: ' . $migration->progress_percent . '%',
            ['step' => $newStep]
        );

        // If completed, update client
        if ($newStep === 'completed' && $migration->client) {
            $migration->update([
                'signoff_by' => auth()->id(),
                'signoff_at' => now(),
                'old_system_status' => 'decommissioned',
            ]);
            $migration->client->update([
                'pipeline_stage'        => 'active',
                'implementation_status' => 'completed',
                'is_live'               => true,
                'activation_status'     => 'active',
                'activation_date'       => now(),
            ]);
            CrmActivity::log('client', $migration->client_id, 'conversion',
                '🏆 Migration completed! Client is now Active Paid.',
                null
            );
        }

        Notification::make()
            ->title('✅ Step Advanced!')
            ->body(CrmMigration::STEP_LABELS[$newStep] ?? $newStep)
            ->success()
            ->send();

        $this->redirect(CrmMigrationResource::getUrl('view', ['record' => $migration]));
    }
}
