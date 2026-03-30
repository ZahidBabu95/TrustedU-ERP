<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Client;
use App\Models\CrmActivity;
use App\Models\CrmMigration;
use App\Models\CrmProposal;
use App\Models\ErpModule;
use App\Models\Lead;
use App\Models\NegotiationLog;
use App\Models\WorkOrder;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewLead extends ViewRecord
{
    protected static string $resource = LeadResource::class;
    protected string $view = 'filament.resources.leads.pages.view-lead';

    public array $contactData = [];
    public array $proposalData = [];
    public array $negotiationData = [];
    public string $lostReason = '';

    public function mount(int|string $record): void
    {
        parent::mount($record);

        $lead = $this->record;
        $existing = $lead->contact_report ?? [];

        $this->contactData = [
            'institution_name'          => $existing['institution_name'] ?? $lead->institute_name ?? '',
            'institution_address'       => $existing['institution_address'] ?? '',
            'institution_type'          => $existing['institution_type'] ?? $lead->institute_type ?? '',
            'decision_maker_name'       => $existing['decision_maker_name'] ?? '',
            'decision_maker_designation'=> $existing['decision_maker_designation'] ?? '',
            'decision_maker_phone'      => $existing['decision_maker_phone'] ?? '',
            'student_count'             => $existing['student_count'] ?? $lead->student_count ?? '',
            'contacted_person'          => $existing['contacted_person'] ?? '',
            'contacted_at'              => $existing['contacted_at'] ?? now()->format('Y-m-d\TH:i'),
            'conversation_summary'      => $existing['conversation_summary'] ?? '',
            'has_previous_software'     => $existing['has_previous_software'] ?? false,
            'previous_software_name'    => $existing['previous_software_name'] ?? '',
            'switch_reason'             => $existing['switch_reason'] ?? '',
            'desired_modules'           => $existing['desired_modules'] ?? [],
            'primary_needs'             => $existing['primary_needs'] ?? '',
            'budget_range'              => $existing['budget_range'] ?? '',
            'interest_assessment'       => $existing['interest_assessment'] ?? '',
            'follow_up_date'            => $existing['follow_up_date'] ?? '',
            'additional_notes'          => $existing['additional_notes'] ?? '',
            'conversion_probability'    => $existing['conversion_probability'] ?? '',
            'conversion_comment'        => $existing['conversion_comment'] ?? '',
        ];

        // Initialize proposal data from existing or contact report
        $existingProposal = $lead->proposal_data ?? [];
        $report = $lead->contact_report ?? [];
        $team = $lead->team;

        $this->proposalData = [
            // Client info (auto-filled)
            'client_name'         => $existingProposal['client_name'] ?? ($report['institution_name'] ?? $lead->institute_name ?? $lead->name),
            'contact_person'      => $existingProposal['contact_person'] ?? ($report['decision_maker_name'] ?? ''),
            'contact_designation' => $existingProposal['contact_designation'] ?? ($report['decision_maker_designation'] ?? ''),
            'client_address'      => $existingProposal['client_address'] ?? ($report['institution_address'] ?? ''),

            // Proposal info
            'title'               => $existingProposal['title'] ?? 'School Management Software (eduERP)',
            'subject'             => $existingProposal['subject'] ?? 'Proposal for School Management Software (eduERP)',
            'introduction'        => $existingProposal['introduction'] ?? "We are pleased to present this proposal for the implementation of our comprehensive School Management Software (eduERP). Our solution will help digitize your institution's operations, improve efficiency, and provide real-time insights for better decision-making.",
            'solution_description'=> $existingProposal['solution_description'] ?? "Based on your requirements, we will implement a complete School Management System including all core modules. The system will be fully customized to your institution's needs and will include training and ongoing support.",

            // Modules
            'modules'             => $existingProposal['modules'] ?? ($report['desired_modules'] ?? []),

            // Features (repeater)
            'features'            => $existingProposal['features'] ?? [
                ['title' => 'Complete Digital Management', 'description' => 'Full student lifecycle management from admission to graduation'],
                ['title' => 'Real-time Reports & Analytics', 'description' => 'Instant access to all institutional data and performance metrics'],
                ['title' => 'Mobile App Access', 'description' => 'Dedicated mobile apps for parents, teachers and administrators'],
                ['title' => 'SMS & Email Notifications', 'description' => 'Automated communication system for all stakeholders'],
            ],

            // Technical
            'tech_frontend'       => $existingProposal['tech_frontend'] ?? 'Web Application (Responsive)',
            'tech_backend'        => $existingProposal['tech_backend'] ?? 'Laravel Secure ERP System',
            'tech_database'       => $existingProposal['tech_database'] ?? 'MySQL',
            'tech_access'         => $existingProposal['tech_access'] ?? 'Web + Mobile App',

            // Pricing
            'setup_cost'          => $existingProposal['setup_cost'] ?? '',
            'monthly_fee'         => $existingProposal['monthly_fee'] ?? '',
            'base_price'          => $existingProposal['base_price'] ?? '',
            'discount_percent'    => $existingProposal['discount_percent'] ?? '0',

            // Timeline & Terms
            'implementation_days' => $existingProposal['implementation_days'] ?? '30',
            'validity_days'       => $existingProposal['validity_days'] ?? '15',
            'payment_terms'       => $existingProposal['payment_terms'] ?? "50% Advance on agreement\n30% after UAT (User Acceptance Testing)\n20% on Final Delivery",
            'support_terms'       => $existingProposal['support_terms'] ?? "24-hour response time for critical issues\nRemote + Onsite support available\nComprehensive training included\nMonthly system updates",

            // Prepared By
            'prepared_by'         => $existingProposal['prepared_by'] ?? auth()->user()->name,
            'prepared_by_designation' => $existingProposal['prepared_by_designation'] ?? '',
            'special_notes'       => $existingProposal['special_notes'] ?? '',
        ];

        // Initialize negotiation form
        $this->negotiationData = [
            'discussion_date' => now()->format('Y-m-d\TH:i'),
            'discussion_type' => 'phone',
            'summary'         => '',
            'counter_offer'   => '',
            'module_changes'  => '',
            'client_response' => '',
            'next_action'     => '',
        ];
    }

    public function getErpModules(): array
    {
        return ErpModule::active()->ordered()->pluck('name', 'slug')->toArray();
    }

    public function getSteps(): array
    {
        return [
            ['key' => 'new',         'label' => 'New',         'icon' => '✨'],
            ['key' => 'contacted',   'label' => 'Contacted',   'icon' => '📞'],
            ['key' => 'qualified',   'label' => 'Qualified',   'icon' => '✅'],
            ['key' => 'proposal',    'label' => 'Proposal',    'icon' => '📄'],
            ['key' => 'negotiation', 'label' => 'Negotiation', 'icon' => '🤝'],
            ['key' => 'won',         'label' => 'Won',         'icon' => '🏆'],
        ];
    }

    // ── View a completed step (read-only, no status change) ──
    public string $viewingStep = '';

    public function viewStep(string $step): void
    {
        $lead = $this->record;
        $stepOrder = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won'];
        $currentIdx = array_search($lead->status, $stepOrder);
        $targetIdx  = array_search($step, $stepOrder);

        if ($targetIdx === false || $currentIdx === false || $targetIdx >= $currentIdx) {
            return;
        }

        $this->viewingStep = $step;
    }

    public function clearViewStep(): void
    {
        $this->viewingStep = '';
    }

    // ── Actually revert to a previous step for editing ──
    public function editStep(string $targetStatus): void
    {
        $lead = $this->record;
        $stepOrder = ['new', 'contacted', 'qualified', 'proposal', 'negotiation', 'won'];
        $currentIdx = array_search($lead->status, $stepOrder);
        $targetIdx  = array_search($targetStatus, $stepOrder);

        if ($targetIdx === false || $currentIdx === false || $targetIdx >= $currentIdx) {
            Notification::make()->title('Invalid step.')->warning()->send();
            return;
        }

        $oldLabel = Lead::STATUS_LABELS[$lead->status] ?? $lead->status;
        $newLabel = Lead::STATUS_LABELS[$targetStatus] ?? $targetStatus;

        $lead->update([
            'status'            => $targetStatus,
            'status_changed_at' => now(),
        ]);

        CrmActivity::log('lead', $lead->id, 'stage_change',
            "Status Reverted for Edit: {$oldLabel} → {$newLabel}",
            "Reverted by " . auth()->user()->name,
            ['old_status' => $lead->status, 'new_status' => $targetStatus, 'reverted_by' => auth()->id()]
        );

        Notification::make()
            ->title("✏️ Editing {$newLabel}")
            ->body("Status reverted to {$newLabel} for editing.")
            ->success()
            ->send();

        $this->viewingStep = '';
        $this->redirect(LeadResource::getUrl('view', ['record' => $lead]));
    }

    // ── Step 1: New → Contacted ──
    public function advanceToContacted(): void
    {
        $lead = $this->record;

        if ($lead->status !== 'new') {
            Notification::make()->title('This lead is already beyond "New" status.')->warning()->send();
            return;
        }

        if (empty($this->contactData['institution_name'])) {
            Notification::make()->title('Institution name is required.')->danger()->send();
            return;
        }
        if (empty($this->contactData['conversation_summary'])) {
            Notification::make()->title('Conversation summary is required.')->danger()->send();
            return;
        }
        if (empty($this->contactData['decision_maker_name'])) {
            Notification::make()->title('Decision maker name is required.')->danger()->send();
            return;
        }

        $lead->update([
            'contact_report'    => $this->contactData,
            'status'            => 'contacted',
            'pipeline_stage'    => 'contacted',
            'status_changed_at' => now(),
            'institute_name'    => $this->contactData['institution_name'],
        ]);

        CrmActivity::log('lead', $lead->id, 'stage_change',
            "Status: New → Contacted | Contact Report submitted",
            "Institution: {$this->contactData['institution_name']}, Decision Maker: {$this->contactData['decision_maker_name']}, Conversion: {$this->contactData['conversion_probability']}%",
            ['contact_report' => $this->contactData]
        );

        Notification::make()
            ->title('✅ Contact Report Saved!')
            ->body('Lead advanced to "Contacted". Awaiting CEO/Head review.')
            ->success()
            ->send();

        $this->redirect(LeadResource::getUrl('view', ['record' => $lead]));
    }

    // ── Step 2: Contacted → Qualified ──
    public function qualifyLead(): void
    {
        $lead = $this->record;

        if ($lead->status !== 'contacted') {
            Notification::make()->title('Only Contacted leads can be qualified.')->warning()->send();
            return;
        }

        $lead->update([
            'status'            => 'qualified',
            'pipeline_stage'    => 'qualified',
            'status_changed_at' => now(),
        ]);

        CrmActivity::log('lead', $lead->id, 'stage_change',
            "Status: Contacted → Qualified | Approved by " . auth()->user()->name,
            "Lead qualified after report review",
            ['qualified_by' => auth()->id()]
        );

        Notification::make()
            ->title('✅ Lead Qualified!')
            ->body('Proposal creation can now begin.')
            ->success()
            ->send();

        $this->redirect(LeadResource::getUrl('view', ['record' => $lead]));
    }

    // ── Step 3: Qualified → Proposal (Save & Advance) ──
    public function saveProposal(): void
    {
        $lead = $this->record;

        if ($lead->status !== 'qualified') {
            Notification::make()->title('Only Qualified leads can create proposals.')->warning()->send();
            return;
        }

        if (empty($this->proposalData['modules'])) {
            Notification::make()->title('At least one module must be selected.')->danger()->send();
            return;
        }
        if (empty($this->proposalData['base_price']) || $this->proposalData['base_price'] <= 0) {
            Notification::make()->title('Base price is required.')->danger()->send();
            return;
        }

        // Calculate final price
        $base = (float) $this->proposalData['base_price'];
        $discountPct = (float) ($this->proposalData['discount_percent'] ?? 0);
        $discountAmt = $base * $discountPct / 100;
        $finalPrice = max(0, $base - $discountAmt);

        // Save proposal data to lead
        $lead->update([
            'proposal_data'     => $this->proposalData,
            'status'            => 'proposal',
            'pipeline_stage'    => 'qualified',
            'status_changed_at' => now(),
            'value'             => $finalPrice,
        ]);

        // Create CRM Proposal record
        CrmProposal::create([
            'lead_id'             => $lead->id,
            'deal_id'             => $lead->deals()->first()?->id,
            'version'             => 1,
            'title'               => $this->proposalData['title'],
            'modules_included'    => $this->proposalData['modules'],
            'base_price'          => $base,
            'discount_percent'    => $discountPct,
            'discount_amount'     => $discountAmt,
            'final_price'         => $finalPrice,
            'implementation_days' => (int) ($this->proposalData['implementation_days'] ?? 30),
            'payment_terms'       => $this->proposalData['payment_terms'],
            'validity_days'       => (int) ($this->proposalData['validity_days'] ?? 15),
            'status'              => 'draft',
            'notes'               => $this->proposalData['special_notes'],
            'created_by'          => auth()->id(),
        ]);

        CrmActivity::log('lead', $lead->id, 'stage_change',
            "Status: Qualified → Proposal | Proposal created",
            "Title: {$this->proposalData['title']}, Amount: ৳" . number_format($finalPrice),
            ['proposal_data' => $this->proposalData, 'final_price' => $finalPrice]
        );

        Notification::make()
            ->title('📄 Proposal Created!')
            ->body("Total: ৳" . number_format($finalPrice) . " — Ready to send.")
            ->success()
            ->send();

        $this->redirect(LeadResource::getUrl('view', ['record' => $lead]));
    }

    // ── Step 4: Proposal → Negotiation (Mark as Sent) ──
    public function markProposalSent(): void
    {
        $lead = $this->record;

        if ($lead->status !== 'proposal') {
            Notification::make()->title('Invalid status.')->warning()->send();
            return;
        }

        $lead->update([
            'status'            => 'negotiation',
            'status_changed_at' => now(),
        ]);

        // Update proposal status to sent
        $proposal = CrmProposal::where('lead_id', $lead->id)->latest()->first();
        $proposal?->markSent();

        CrmActivity::log('lead', $lead->id, 'stage_change',
            "Status: Proposal → Negotiation | Proposal sent to client",
            null,
            ['proposal_id' => $proposal?->id]
        );

        Notification::make()
            ->title('📧 Proposal Sent!')
            ->body('Lead moved to Negotiation stage.')
            ->success()
            ->send();

        $this->redirect(LeadResource::getUrl('view', ['record' => $lead]));
    }

    // ── Step 5: Negotiation Log ──
    public function addNegotiationLog(): void
    {
        $lead = $this->record;

        if (empty($this->negotiationData['summary'])) {
            Notification::make()->title('Discussion summary is required.')->danger()->send();
            return;
        }

        NegotiationLog::create([
            'lead_id'         => $lead->id,
            'discussion_date' => $this->negotiationData['discussion_date'],
            'discussion_type' => $this->negotiationData['discussion_type'],
            'summary'         => $this->negotiationData['summary'],
            'counter_offer'   => !empty($this->negotiationData['counter_offer']) ? $this->negotiationData['counter_offer'] : null,
            'module_changes'  => $this->negotiationData['module_changes'] ?: null,
            'client_response' => $this->negotiationData['client_response'] ?: null,
            'next_action'     => $this->negotiationData['next_action'] ?: null,
            'logged_by'       => auth()->id(),
        ]);

        CrmActivity::log('lead', $lead->id, 'negotiation',
            "Negotiation: " . (NegotiationLog::TYPE_LABELS[$this->negotiationData['discussion_type']] ?? $this->negotiationData['discussion_type']),
            $this->negotiationData['summary'],
            $this->negotiationData
        );

        Notification::make()
            ->title('🤝 Negotiation Log Added!')
            ->success()
            ->send();

        // Reset form
        $this->negotiationData = [
            'discussion_date' => now()->format('Y-m-d\TH:i'),
            'discussion_type' => 'phone',
            'summary'         => '',
            'counter_offer'   => '',
            'module_changes'  => '',
            'client_response' => '',
            'next_action'     => '',
        ];

        $this->redirect(LeadResource::getUrl('view', ['record' => $lead]));
    }

    // ── Step 6: Negotiation → Won (Create Trial Client + Migration) ──
    public function markAsWon(): void
    {
        $lead = $this->record;

        if ($lead->status !== 'negotiation') {
            Notification::make()->title('Only Negotiation leads can be marked as Won.')->warning()->send();
            return;
        }

        try {
            \Illuminate\Support\Facades\DB::transaction(function () use ($lead) {
                $proposalData = $lead->proposal_data ?? [];
                $report = $lead->contact_report ?? [];
                $proposal = CrmProposal::where('lead_id', $lead->id)->latest()->first();
                $finalPrice = $proposal?->final_price ?? $lead->value ?? 0;

                // Mark lead as Won
                $lead->update([
                    'status'            => 'won',
                    'pipeline_stage'    => 'qualified',
                    'status_changed_at' => now(),
                ]);

                // Approve proposal
                $proposal?->markApproved();

                // Generate Work Order
                $workOrder = WorkOrder::create([
                    'lead_id'           => $lead->id,
                    'order_number'      => WorkOrder::generateOrderNumber(),
                    'client_name'       => $lead->name,
                    'institute_name'    => $lead->institute_name,
                    'items'             => $proposalData['modules'] ?? [],
                    'total_amount'      => $finalPrice,
                    'start_date'        => now()->addDays(3)->toDateString(),
                    'expected_delivery' => now()->addDays((int)($proposalData['implementation_days'] ?? 30))->toDateString(),
                    'payment_terms'     => $proposalData['payment_terms'] ?? null,
                    'deliverables'      => collect($proposalData['modules'] ?? [])->map(fn($m) => ErpModule::where('slug', $m)->value('name') ?? $m)->implode(', '),
                    'status'            => 'generated',
                    'generated_by'      => auth()->id(),
                ]);

                // Sanitize institution_type for ENUM
                $allowedTypes = ['school', 'college', 'school_and_college', 'school_college', 'university', 'madrasha', 'coaching', 'coaching_center', 'corporate', 'ngo', 'other'];
                $instType = $report['institution_type'] ?? null;
                if ($instType && !in_array($instType, $allowedTypes)) {
                    $instType = 'other';
                }

                // ★ Create Trial Client
                $client = Client::create([
                    'name'                  => $proposalData['client_name'] ?? $lead->institute_name ?? $lead->name,
                    'email'                 => $lead->email,
                    'phone'                 => $lead->phone,
                    'address'               => $proposalData['client_address'] ?? ($report['institution_address'] ?? ''),
                    'institution_type'      => $instType,
                    'principal_name'        => $proposalData['contact_person'] ?? ($report['decision_maker_name'] ?? ''),
                    'principal_phone'       => $report['decision_maker_phone'] ?? '',
                    'lead_id'               => $lead->id,
                    'lead_support_person'   => $lead->assigned_to,
                    'pipeline_stage'        => 'migration',
                    'activation_status'     => 'pending',
                    'implementation_status' => 'not_started',
                    'billing_status'        => 'active',
                    'package_price'         => $finalPrice,
                    'is_active'             => true,
                    'is_live'               => false,
                    'contract_start'        => now(),
                ]);

                // Attach team
                if ($lead->team_id) {
                    $client->teams()->attach($lead->team_id);
                }

                // ★ Create CRM Migration
                $migration = CrmMigration::create([
                    'client_id'             => $client->id,
                    'lead_id'               => $lead->id,
                    'assigned_to'           => $lead->assigned_to ?? auth()->id(),
                    'current_step'          => 'onboarding_plan',
                    'status'                => 'in_progress',
                    'progress_percent'      => 0,
                    'migration_start_date'  => now(),
                    'migration_end_date'    => now()->addDays((int)($proposalData['implementation_days'] ?? 30)),
                    'buffer_days'           => 5,
                    'old_system_status'     => 'running',
                    'checklist_items'       => CrmMigration::DEFAULT_CHECKLIST,
                    'notes'                 => "Auto-created from Lead #{$lead->id} ({$lead->name}). Work Order: {$workOrder->order_number}",
                ]);

                // Generate default migration tasks
                $migration->generateDefaultTasks();

                // Log activities
                CrmActivity::log('lead', $lead->id, 'stage_change',
                    "Status: Negotiation → Won 🏆 | Client & Migration Created",
                    "Client ID: {$client->client_id}, Work Order: {$workOrder->order_number}, Amount: ৳" . number_format($finalPrice),
                    ['work_order_id' => $workOrder->id, 'client_id' => $client->id, 'migration_id' => $migration->id]
                );

                CrmActivity::log('client', $client->id, 'conversion',
                    "🏆 Trial Client created from Lead #{$lead->id}",
                    "Migration started. Pipeline: Onboarding Plan",
                    ['lead_id' => $lead->id, 'migration_id' => $migration->id]
                );

                Notification::make()
                    ->title('🏆 Lead Won!')
                    ->body("Client {$client->client_id} created. Migration started. Work Order: {$workOrder->order_number}")
                    ->success()
                    ->send();
            });
        } catch (\Exception $e) {
            Notification::make()
                ->title('❌ Error')
                ->body('Something went wrong: ' . $e->getMessage())
                ->danger()
                ->send();
            return;
        }

        $this->redirect(LeadResource::getUrl('view', ['record' => $lead]));
    }

    // ── Generic advance (legacy, kept for safety) ──
    public function advanceStatus(): void
    {
        $lead = $this->record;
        $flow = ['qualified' => 'proposal', 'proposal' => 'negotiation', 'negotiation' => 'won'];
        $next = $flow[$lead->status] ?? null;

        if (!$next) {
            Notification::make()->title('No further advancement possible.')->info()->send();
            return;
        }

        $old = $lead->status;
        $lead->update([
            'status'            => $next,
            'status_changed_at' => now(),
        ]);

        CrmActivity::log('lead', $lead->id, 'stage_change',
            "Status: " . (Lead::STATUS_LABELS[$old] ?? $old) . " → " . (Lead::STATUS_LABELS[$next] ?? $next),
            null,
            ['old_status' => $old, 'new_status' => $next]
        );

        Notification::make()
            ->title('✅ Status Updated!')
            ->body((Lead::STATUS_LABELS[$old] ?? $old) . ' → ' . (Lead::STATUS_LABELS[$next] ?? $next))
            ->success()
            ->send();

        $this->redirect(LeadResource::getUrl('view', ['record' => $lead]));
    }

    // ── Mark Lost ──
    public function markLost(): void
    {
        $lead = $this->record;

        if (empty($this->lostReason)) {
            Notification::make()->title('Reason is required.')->danger()->send();
            return;
        }

        $old = $lead->status;
        $lead->update([
            'status'            => 'lost',
            'pipeline_stage'    => 'lost',
            'lost_reason'       => $this->lostReason,
            'lost_at'           => now(),
            'status_changed_at' => now(),
        ]);

        CrmActivity::log('lead', $lead->id, 'stage_change',
            "Status: " . (Lead::STATUS_LABELS[$old] ?? $old) . " → Lost",
            "Reason: {$this->lostReason}",
            ['old_status' => $old, 'lost_reason' => $this->lostReason]
        );

        Notification::make()->title('❌ Lead marked as Lost')->warning()->send();
        $this->redirect(LeadResource::getUrl('index'));
    }
}
