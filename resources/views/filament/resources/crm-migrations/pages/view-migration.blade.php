<x-filament-panels::page>
@php
    $migration = $record;
    $client = $migration->client;
    $currentStep = $migration->current_step;
    $steps = \App\Models\CrmMigration::PIPELINE_STEPS;
    $stepLabels = \App\Models\CrmMigration::STEP_LABELS;
    $stepIcons = \App\Models\CrmMigration::STEP_ICONS;
    $currentIdx = array_search($currentStep, $steps);
    $isCompleted = $currentStep === 'completed';
    $allTasks = $migration->tasks()->orderBy('sort_order')->get();
    $checklist = $migration->checklist_items ?? \App\Models\CrmMigration::DEFAULT_CHECKLIST;
    $plan = $migration->onboarding_plan ?? [];
    $training = $migration->training_data ?? [];
    $handover = $migration->handover_data ?? [];
    $activeView = $viewingStep ?: $currentStep;
@endphp

<div class="mg" x-data="{ showTaskForm: false }">
<div class="mg-inner">

    {{-- ═══ HEADER ═══ --}}
    <div class="mg-head">
        <div class="mg-head-info">
            <div class="mg-avatar">🔄</div>
            <div>
                <div class="mg-title">{{ $client?->name ?? 'Unknown Client' }}</div>
                <div class="mg-meta">Client ID: <b>{{ $client?->client_id ?? '—' }}</b> · Migration #{{ $migration->id }} · {{ $migration->assignee?->name ?? '—' }}</div>
            </div>
        </div>
        <div class="mg-badges">
            <span class="mg-badge mg-badge--progress">{{ $migration->progress_percent }}% Complete</span>
            @if($migration->migration_end_date)
                <span class="mg-badge">📅 Target: {{ $migration->migration_end_date->format('d M Y') }}</span>
            @endif
        </div>
    </div>

    {{-- ═══ STEP TIMELINE ═══ --}}
    <div class="mg-timeline">
        @foreach($steps as $i => $step)
            @php
                $done = $i < $currentIdx;
                $active = $step === $currentStep;
                $viewable = $done || $active;
                $isViewing = $activeView === $step;
            @endphp
            <div class="mg-step {{ $done ? 'mg-step--done' : ($active ? 'mg-step--active' : 'mg-step--pending') }} {{ $isViewing ? 'mg-step--viewing' : '' }}"
                @if($viewable) wire:click="viewStep('{{ $step }}')" style="cursor:pointer" @endif>
                <div class="mg-step__dot">
                    @if($done)✅ @elseif($active)🔵 @else⬜ @endif
                </div>
                <div class="mg-step__label">{{ $stepLabels[$step] ?? $step }}</div>
            </div>
            @if(!$loop->last)<div class="mg-step__line {{ $done ? 'mg-step__line--done' : '' }}"></div>@endif
        @endforeach
    </div>

    {{-- ═══ VIEWING STEP INDICATOR ═══ --}}
    @if($viewingStep && $viewingStep !== $currentStep)
        <div class="mg-card mg-card--info" style="display:flex;align-items:center;justify-content:space-between">
            <span>👁️ Viewing: <b>{{ $stepLabels[$viewingStep] ?? $viewingStep }}</b> (Read Only)</span>
            <button wire:click="clearViewingStep" class="mg-btn-sm" type="button">← Back to Current Step</button>
        </div>
    @endif

    {{-- ═══ TASK LIST (Tasks for current/viewed step) ═══ --}}
    @php
        $stepCategoryMap = [
            'onboarding_plan' => 'onboarding',
            'data_processing' => 'data_processing',
            'system_entry' => 'system_entry',
            'onboarding_checklist' => 'verification',
            'training' => 'training',
            'handover' => 'handover',
            'invoice_deed' => 'invoice',
        ];
        $viewCategory = $stepCategoryMap[$activeView] ?? 'onboarding';
        $stepTasks = $allTasks->where('task_category', $viewCategory);
        $completedCount = $stepTasks->where('status', 'completed')->count();
        $totalCount = $stepTasks->count();
    @endphp

    {{-- ═══ STEP 1: ONBOARDING PLAN ═══ --}}
    @if($activeView === 'onboarding_plan')
        <div class="mg-card">
            <div class="mg-card__head">📋 Onboarding Plan</div>
            <p class="mg-card__sub">প্রতিষ্ঠান থেকে ডেটা সংগ্রহের পদ্ধতি ও পরিকল্পনা নথিভুক্ত করুন।</p>
            <form wire:submit.prevent="saveOnboardingPlan" class="mg-form">
                <div class="mg-grid mg-grid--2">
                    <div class="mg-input-group">
                        <label>ডেটা টাইপ <abbr>*</abbr></label>
                        <select wire:model="onboardingPlan.data_type" {{ $viewingStep && $viewingStep !== $currentStep ? 'disabled' : '' }}>
                            <option value="">— Select —</option>
                            @foreach(\App\Models\CrmMigration::DATA_TYPE_LABELS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                        </select>
                    </div>
                    <div class="mg-input-group">
                        <label>ডেটা সোর্স</label>
                        <input type="text" wire:model="onboardingPlan.data_source" placeholder="যেমন: পূর্বের সফটওয়্যার, Excel ফাইল, রেজিস্টার" {{ $viewingStep && $viewingStep !== $currentStep ? 'disabled' : '' }}>
                    </div>
                </div>
                <div class="mg-input-group">
                    <label>কনভার্সন প্ল্যান</label>
                    <textarea wire:model="onboardingPlan.conversion_plan" rows="3" placeholder="হার্ড কপি হলে কীভাবে সফট কপি করা হবে, সময়সীমা ইত্যাদি" {{ $viewingStep && $viewingStep !== $currentStep ? 'disabled' : '' }}></textarea>
                </div>
                <div class="mg-grid mg-grid--3">
                    <div class="mg-input-group"><label>আনুমানিক রেকর্ড সংখ্যা</label>
                        <input type="text" wire:model="onboardingPlan.estimated_records" placeholder="যেমন: 500 ছাত্র, 30 শিক্ষক" {{ $viewingStep && $viewingStep !== $currentStep ? 'disabled' : '' }}></div>
                    <div class="mg-input-group"><label>যোগাযোগ ব্যক্তি</label>
                        <input type="text" wire:model="onboardingPlan.contact_person" {{ $viewingStep && $viewingStep !== $currentStep ? 'disabled' : '' }}></div>
                    <div class="mg-input-group"><label>ফোন</label>
                        <input type="text" wire:model="onboardingPlan.contact_phone" {{ $viewingStep && $viewingStep !== $currentStep ? 'disabled' : '' }}></div>
                </div>
                <div class="mg-input-group"><label>বিশেষ নোট</label>
                    <textarea wire:model="onboardingPlan.special_notes" rows="2" {{ $viewingStep && $viewingStep !== $currentStep ? 'disabled' : '' }}></textarea>
                </div>
                @if(!$viewingStep || $viewingStep === $currentStep)
                    <div class="mg-form-footer">
                        <button type="submit" class="mg-btn">💾 Save Onboarding Plan</button>
                    </div>
                @endif
            </form>
        </div>
    @endif

    {{-- ═══ STEP 4: ONBOARDING CHECKLIST ═══ --}}
    @if($activeView === 'onboarding_checklist')
        <div class="mg-card">
            <div class="mg-card__head">✅ Onboarding Checklist</div>
            <p class="mg-card__sub">সকল চেকপয়েন্ট টিক করুন।</p>
            <div class="mg-checklist">
                @foreach($checklist as $idx => $item)
                    <div class="mg-check-item {{ $item['done'] ? 'mg-check-item--done' : '' }}" wire:click="toggleChecklist({{ $idx }})" style="cursor:pointer">
                        <span class="mg-check-box">{{ $item['done'] ? '✅' : '⬜' }}</span>
                        <span>{{ $item['item'] }}</span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ═══ STEP 5: TRAINING ═══ --}}
    @if($activeView === 'training')
        <div class="mg-card">
            <div class="mg-card__head">🎓 Training Information</div>
            <form wire:submit.prevent="saveTrainingData" class="mg-form">
                <div class="mg-grid mg-grid--2">
                    <div class="mg-input-group"><label>Trainer Name</label>
                        <input type="text" wire:model="trainingForm.trainer_name" placeholder="প্রশিক্ষকের নাম"></div>
                    <div class="mg-input-group"><label>Training Date</label>
                        <input type="date" wire:model="trainingForm.training_date"></div>
                </div>
                <div class="mg-grid mg-grid--2">
                    <div class="mg-input-group"><label>Duration</label>
                        <input type="text" wire:model="trainingForm.duration" placeholder="যেমন: 3 ঘণ্টা"></div>
                    <div class="mg-input-group"><label>Attendees</label>
                        <input type="text" wire:model="trainingForm.attendees" placeholder="অংশগ্রহণকারী সংখ্যা/নাম"></div>
                </div>
                <div class="mg-input-group"><label>Training Topics</label>
                    <textarea wire:model="trainingForm.topics" rows="2" placeholder="কোন কোন বিষয়ে ট্রেনিং দেওয়া হয়েছে"></textarea></div>
                <div class="mg-input-group"><label>Notes</label>
                    <textarea wire:model="trainingForm.notes" rows="2"></textarea></div>
                <div class="mg-form-footer"><button type="submit" class="mg-btn">💾 Save Training Data</button></div>
            </form>
        </div>
    @endif

    {{-- ═══ STEP 6: HANDOVER ═══ --}}
    @if($activeView === 'handover')
        <div class="mg-card">
            <div class="mg-card__head">🤝 Handover Information</div>
            <form wire:submit.prevent="saveHandoverData" class="mg-form">
                <div class="mg-grid mg-grid--2">
                    <div class="mg-input-group"><label>Handover Date</label>
                        <input type="date" wire:model="handoverForm.handover_date"></div>
                    <div class="mg-input-group"><label>Handed Over To</label>
                        <input type="text" wire:model="handoverForm.handed_to" placeholder="যাকে হ্যান্ডওভার করা হয়েছে"></div>
                </div>
                <div class="mg-input-group"><label>Credentials Provided</label>
                    <textarea wire:model="handoverForm.credentials_info" rows="2" placeholder="Admin URL, Login details ইত্যাদি"></textarea></div>
                <div class="mg-input-group"><label>Notes</label>
                    <textarea wire:model="handoverForm.notes" rows="2"></textarea></div>
                <div class="mg-form-footer"><button type="submit" class="mg-btn">💾 Save Handover Data</button></div>
            </form>
        </div>
    @endif

    {{-- ═══ STEP 7: INVOICE & DEED ═══ --}}
    @if($activeView === 'invoice_deed')
        <div class="mg-card">
            <div class="mg-card__head">📄 Invoice & Deed</div>
            <p class="mg-card__sub">ইনভয়েস ও চুক্তিপত্র চূড়ান্ত করে পাঠান। পেমেন্ট পেলে Migration সম্পন্ন।</p>
            <div class="mg-data-grid">
                <div class="mg-data"><label>Package Price</label><span style="font-weight:700;color:#059669;font-size:15px">৳{{ number_format($client?->package_price ?? 0) }}</span></div>
                <div class="mg-data"><label>Client</label><span>{{ $client?->name ?? '—' }}</span></div>
                <div class="mg-data"><label>Contract Start</label><span>{{ $client?->contract_start?->format('d M Y') ?? '—' }}</span></div>
            </div>
        </div>
    @endif

    {{-- ═══ COMPLETED ═══ --}}
    @if($activeView === 'completed' || $isCompleted)
        <div class="mg-card" style="border:2px solid #22c55e;background:linear-gradient(135deg,#f0fdf4,#dcfce7)">
            <div class="mg-card__head" style="color:#16a34a">🏆 Migration Completed!</div>
            <p class="mg-card__sub" style="color:#15803d">এই মাইগ্রেশন সফলভাবে সম্পন্ন হয়েছে। ক্লায়েন্ট এখন Active Paid Client।</p>
            <div class="mg-data-grid">
                <div class="mg-data"><label>Completed At</label><span>{{ $migration->actual_end_date?->format('d M Y') ?? '—' }}</span></div>
                <div class="mg-data"><label>Signed Off By</label><span>{{ $migration->signoffUser?->name ?? '—' }}</span></div>
                <div class="mg-data"><label>Total Tasks</label><span>{{ $allTasks->count() }} ({{ $allTasks->where('status','completed')->count() }} done)</span></div>
            </div>
        </div>
    @endif

    {{-- ═══ TASK LIST FOR CURRENT STEP ═══ --}}
    @if($activeView !== 'completed')
        <div class="mg-card">
            <div class="mg-card__head" style="display:flex;justify-content:space-between;align-items:center">
                <span>📝 Tasks — {{ $stepLabels[$activeView] ?? $activeView }} ({{ $completedCount }}/{{ $totalCount }})</span>
                @if(!$viewingStep || $viewingStep === $currentStep)
                    <button @click="showTaskForm = !showTaskForm" class="mg-btn-sm" type="button">➕ Add Task</button>
                @endif
            </div>

            @if($stepTasks->count() > 0)
                <div class="mg-task-list">
                    @foreach($stepTasks as $task)
                        <div class="mg-task {{ $task->status === 'completed' ? 'mg-task--done' : '' }}">
                            <div class="mg-task__left">
                                @if($task->status !== 'completed' && (!$viewingStep || $viewingStep === $currentStep))
                                    <button wire:click="completeTask({{ $task->id }})" class="mg-task__check" title="Mark Complete" wire:confirm="Complete this task?">⬜</button>
                                @else
                                    <span class="mg-task__check mg-task__check--done">✅</span>
                                @endif
                                <div>
                                    <div class="mg-task__title">{{ $task->title }}</div>
                                    @if($task->description)<div class="mg-task__desc">{{ $task->description }}</div>@endif
                                    <div class="mg-task__meta">
                                        <span class="mg-chip mg-chip--{{ $task->priority }}">{{ \App\Models\CrmMigrationTask::PRIORITY_LABELS[$task->priority] ?? $task->priority }}</span>
                                        @if($task->due_date)<span>📅 {{ $task->due_date->format('d M') }}</span>@endif
                                        @if($task->file_path)<a href="{{ asset('storage/' . $task->file_path) }}" target="_blank" class="mg-chip mg-chip--file">📎 File</a>@endif
                                        @if($task->completed_at)<span style="color:#16a34a">✔ {{ $task->completed_at->format('d M H:i') }}</span>@endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p style="padding:16px;color:#9ca3af;font-size:13px">No tasks for this step yet.</p>
            @endif
        </div>

        {{-- ═══ ADD TASK FORM ═══ --}}
        @if(!$viewingStep || $viewingStep === $currentStep)
            <div x-show="showTaskForm" x-collapse class="mg-card">
                <div class="mg-card__head">➕ Add New Task</div>
                <form wire:submit.prevent="addTask" class="mg-form">
                    <div class="mg-grid mg-grid--2">
                        <div class="mg-input-group"><label>Title <abbr>*</abbr></label>
                            <input type="text" wire:model="taskForm.title" required placeholder="Task title"></div>
                        <div class="mg-input-group"><label>Category</label>
                            <select wire:model="taskForm.task_category">
                                @foreach(\App\Models\CrmMigrationTask::CATEGORY_LABELS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select></div>
                    </div>
                    <div class="mg-input-group"><label>Description</label>
                        <textarea wire:model="taskForm.description" rows="2" placeholder="বিস্তারিত বর্ণনা"></textarea></div>
                    <div class="mg-grid mg-grid--3">
                        <div class="mg-input-group"><label>Priority</label>
                            <select wire:model="taskForm.priority">
                                @foreach(\App\Models\CrmMigrationTask::PRIORITY_LABELS as $k => $v)<option value="{{ $k }}">{{ $v }}</option>@endforeach
                            </select></div>
                        <div class="mg-input-group"><label>Due Date</label>
                            <input type="date" wire:model="taskForm.due_date"></div>
                        <div class="mg-input-group"><label>File Attachment</label>
                            <input type="file" wire:model="taskFile" style="font-size:12px"></div>
                    </div>
                    <div class="mg-input-group"><label>Notes</label>
                        <textarea wire:model="taskForm.notes" rows="2" placeholder="অতিরিক্ত নোট"></textarea></div>
                    <div class="mg-form-footer">
                        <button type="submit" class="mg-btn">✅ Add Task</button>
                        <button @click="showTaskForm = false" class="mg-btn mg-btn--outline" type="button">Cancel</button>
                    </div>
                </form>
            </div>

            {{-- ═══ ADVANCE STEP BUTTON ═══ --}}
            @if(!$isCompleted)
                <div class="mg-card mg-card--action">
                    @php $nextIdx = $currentIdx + 1; $nextStep = $steps[$nextIdx] ?? null; @endphp
                    @if($nextStep)
                        <button wire:click="advanceStep" class="mg-btn mg-btn--primary" wire:confirm="Advance to {{ $stepLabels[$nextStep] ?? $nextStep }}?">
                            → Advance to {{ $stepLabels[$nextStep] ?? $nextStep }}
                        </button>
                    @endif
                </div>
            @endif
        @endif
    @endif

    {{-- ═══ ACTIVITY LOG ═══ --}}
    @php $activities = \App\Models\CrmActivity::where('entity_type', 'migration')->where('entity_id', $migration->id)->latest()->take(10)->get(); @endphp
    @if($activities->count() > 0)
        <div class="mg-card">
            <div class="mg-card__head">📜 Activity Log</div>
            <div class="mg-activity-list">
                @foreach($activities as $act)
                    <div class="mg-activity">
                        <div class="mg-activity__time">{{ $act->created_at->diffForHumans() }}</div>
                        <div class="mg-activity__title">{{ $act->title }}</div>
                        @if($act->description)<div class="mg-activity__desc">{{ $act->description }}</div>@endif
                    </div>
                @endforeach
            </div>
        </div>
    @endif

</div>
</div>

<style>
:root{--mg-primary:#6366f1;--mg-primary-light:#818cf8;--mg-green:#22c55e;--mg-red:#ef4444;--mg-border:#e5e7eb;--mg-bg:#f9fafb;--mg-card:#fff;--mg-text:#111827;--mg-text2:#6b7280;--mg-radius:10px;--mg-shadow:0 1px 3px rgba(0,0,0,.05)}

.mg{width:100%;font-size:13px;line-height:1.5;color:var(--mg-text)}
.mg-inner{padding:12px 16px}
.mg-inner *,.mg-inner *::before,.mg-inner *::after{box-sizing:border-box}

/* Header */
.mg-head{display:flex;flex-direction:column;gap:10px;padding:14px;background:linear-gradient(135deg,#f0f3ff,#e8ecff);border-radius:var(--mg-radius);margin-bottom:10px}
.mg-head-info{display:flex;align-items:center;gap:10px}
.mg-avatar{width:42px;height:42px;border-radius:10px;background:linear-gradient(135deg,var(--mg-primary),#4f46e5);display:flex;align-items:center;justify-content:center;font-size:20px;color:#fff}
.mg-title{font-size:17px;font-weight:800;color:var(--mg-text)}
.mg-meta{font-size:11px;color:var(--mg-text2);margin-top:1px}
.mg-badges{display:flex;gap:6px;flex-wrap:wrap}
.mg-badge{padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600;background:#e5e7eb;color:var(--mg-text2)}
.mg-badge--progress{background:linear-gradient(135deg,var(--mg-primary),#4f46e5);color:#fff}

/* Timeline */
.mg-timeline{display:flex;align-items:center;gap:0;padding:10px 6px;margin-bottom:10px;overflow-x:auto;-webkit-overflow-scrolling:touch}
.mg-step{display:flex;flex-direction:column;align-items:center;gap:3px;min-width:60px;flex-shrink:0;transition:.2s}
.mg-step:hover{transform:scale(1.05)}
.mg-step__dot{width:28px;height:28px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:12px;border:2px solid #e5e7eb;background:#fff}
.mg-step--done .mg-step__dot{border-color:var(--mg-green);background:#f0fdf4}
.mg-step--active .mg-step__dot{border-color:var(--mg-primary);background:#eef2ff;box-shadow:0 0 0 3px rgba(99,102,241,.15)}
.mg-step--viewing .mg-step__dot{box-shadow:0 0 0 4px rgba(99,102,241,.25)}
.mg-step__label{font-size:9px;font-weight:600;text-align:center;color:var(--mg-text2);max-width:70px;line-height:1.2}
.mg-step--done .mg-step__label{color:var(--mg-green)}
.mg-step--active .mg-step__label{color:var(--mg-primary);font-weight:700}
.mg-step__line{flex:1;height:2px;background:#e5e7eb;min-width:12px}
.mg-step__line--done{background:var(--mg-green)}

/* Cards */
.mg-card{background:var(--mg-card);border:1px solid var(--mg-border);border-radius:var(--mg-radius);padding:14px 16px;margin-bottom:8px;box-shadow:var(--mg-shadow)}
.mg-card__head{font-size:14px;font-weight:700;margin-bottom:6px;color:var(--mg-text)}
.mg-card__sub{font-size:12px;color:var(--mg-text2);margin-bottom:10px}
.mg-card--info{background:#eef2ff;border-color:#c7d2fe;color:var(--mg-primary)}
.mg-card--action{text-align:center;background:#f0f3ff;border:1px dashed var(--mg-primary)}

/* Data Grid */
.mg-data-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:6px;margin:6px 0}
.mg-data{padding:6px 10px;background:var(--mg-bg);border-radius:6px}
.mg-data label{font-size:10px;font-weight:600;color:var(--mg-text2);text-transform:uppercase;letter-spacing:.3px;display:block}
.mg-data span{font-size:13px;color:var(--mg-text)}

/* Form */
.mg-form{display:flex;flex-direction:column;gap:8px}
.mg-grid{display:grid;gap:8px}.mg-grid--2{grid-template-columns:1fr 1fr}.mg-grid--3{grid-template-columns:1fr 1fr 1fr}
@media(max-width:640px){.mg-grid--2,.mg-grid--3{grid-template-columns:1fr}}
.mg-input-group{display:flex;flex-direction:column;gap:2px}
.mg-input-group label{font-size:11px;font-weight:600;color:var(--mg-text2)}
.mg-input-group label abbr{color:var(--mg-red);text-decoration:none}
.mg-input-group input,.mg-input-group select,.mg-input-group textarea{border:1px solid var(--mg-border);border-radius:7px;padding:7px 10px;font-size:13px;background:#fff;font-family:inherit;color:var(--mg-text);transition:.2s}
.mg-input-group input:focus,.mg-input-group select:focus,.mg-input-group textarea:focus{outline:none;border-color:var(--mg-primary);box-shadow:0 0 0 2px rgba(99,102,241,.1)}
.mg-form-footer{display:flex;gap:8px;margin-top:6px;flex-wrap:wrap}

/* Buttons */
.mg-btn{padding:8px 18px;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;font-family:inherit;background:var(--mg-primary);color:#fff;transition:.2s}
.mg-btn:hover{filter:brightness(1.1);transform:translateY(-1px)}
.mg-btn--primary{background:linear-gradient(135deg,var(--mg-primary),#4f46e5);font-size:14px;padding:10px 24px}
.mg-btn--outline{background:transparent;color:var(--mg-text2);border:1px solid var(--mg-border)}
.mg-btn-sm{padding:4px 12px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;background:var(--mg-primary);color:#fff;font-family:inherit}

/* Tasks */
.mg-task-list{display:flex;flex-direction:column;gap:4px}
.mg-task{display:flex;justify-content:space-between;align-items:flex-start;padding:8px 10px;border-bottom:1px solid #f3f4f6;transition:.1s}
.mg-task:hover{background:#f9fafb}
.mg-task--done{opacity:.6}
.mg-task--done .mg-task__title{text-decoration:line-through}
.mg-task__left{display:flex;gap:8px;align-items:flex-start}
.mg-task__check{width:24px;height:24px;border:none;background:none;cursor:pointer;font-size:14px;flex-shrink:0;display:flex;align-items:center;justify-content:center;border-radius:4px;transition:.15s}
.mg-task__check:hover{background:#eef2ff}
.mg-task__check--done{cursor:default}
.mg-task__title{font-size:13px;font-weight:600;color:var(--mg-text)}
.mg-task__desc{font-size:11px;color:var(--mg-text2);margin-top:1px}
.mg-task__meta{display:flex;gap:6px;align-items:center;margin-top:3px;font-size:10px;color:var(--mg-text2);flex-wrap:wrap}

/* Chips */
.mg-chip{padding:2px 8px;border-radius:10px;font-size:10px;font-weight:600;background:#f3f4f6;color:#6b7280}
.mg-chip--low{background:#f1f5f9;color:#94a3b8}
.mg-chip--medium{background:#dbeafe;color:#2563eb}
.mg-chip--high{background:#fff7ed;color:#ea580c}
.mg-chip--critical{background:#fef2f2;color:#dc2626}
.mg-chip--file{background:#f0fdf4;color:#16a34a;cursor:pointer;text-decoration:none}

/* Checklist */
.mg-checklist{display:flex;flex-direction:column;gap:3px}
.mg-check-item{display:flex;align-items:center;gap:8px;padding:7px 10px;border-radius:6px;transition:.15s;font-size:13px}
.mg-check-item:hover{background:#f3f4f6}
.mg-check-item--done{color:var(--mg-green)}
.mg-check-box{font-size:16px}

/* Activity */
.mg-activity-list{display:flex;flex-direction:column;gap:4px;max-height:300px;overflow-y:auto}
.mg-activity{padding:6px 10px;border-bottom:1px solid #f3f4f6}
.mg-activity__time{font-size:10px;color:var(--mg-text2)}
.mg-activity__title{font-size:12px;font-weight:600;color:var(--mg-text)}
.mg-activity__desc{font-size:11px;color:var(--mg-text2);margin-top:1px}

/* Dark Mode */
.dark .mg-head{background:linear-gradient(135deg,#1e1b4b,#312e81)}
.dark .mg-card{background:#1f2937;border-color:#374151}
.dark .mg-card--info{background:#1e1b4b;border-color:#312e81}
.dark .mg-card--action{background:#1e1b4b;border-color:#4f46e5}
.dark .mg-data{background:#111827}
.dark .mg-data label{color:#9ca3af}
.dark .mg-data span{color:#f3f4f6}
.dark .mg-input-group input,.dark .mg-input-group select,.dark .mg-input-group textarea{background:#111827;border-color:#374151;color:#f3f4f6}
.dark .mg-task:hover{background:#111827}
.dark .mg-check-item:hover{background:#111827}
.dark .mg-title{color:#f3f4f6}
</style>
</x-filament-panels::page>
