<?php

namespace App\Services;

use App\Models\CrmActivity;
use App\Models\CrmFollowUp;
use App\Models\CrmMigration;
use App\Models\Client;
use App\Models\Deal;
use App\Models\Lead;

class CrmService
{
    /**
     * Move a lead to the next pipeline stage.
     */
    public function advanceLeadStage(Lead $lead, string $newStage): void
    {
        $oldStage = $lead->pipeline_stage;

        $lead->update(['pipeline_stage' => $newStage]);

        // Sync legacy status
        $stageToStatus = [
            'new_lead'  => 'new',
            'contacted' => 'contacted',
            'qualified' => 'qualified',
            'lost'      => 'lost',
        ];
        if (isset($stageToStatus[$newStage])) {
            $lead->update(['status' => $stageToStatus[$newStage]]);
        }

        CrmActivity::log('lead', $lead->id, 'stage_change',
            "Stage: " . (Lead::PIPELINE_STAGE_LABELS[$oldStage] ?? $oldStage) . " → " . (Lead::PIPELINE_STAGE_LABELS[$newStage] ?? $newStage),
            null,
            ['old_stage' => $oldStage, 'new_stage' => $newStage]
        );
    }

    /**
     * Move a deal to the next pipeline stage.
     */
    public function advanceDealStage(Deal $deal, string $newStage): void
    {
        $oldStage = $deal->pipeline_stage;

        $deal->update(['pipeline_stage' => $newStage]);

        CrmActivity::log('deal', $deal->id, 'stage_change',
            "Stage: " . (Deal::PIPELINE_STAGE_LABELS[$oldStage] ?? $oldStage) . " → " . (Deal::PIPELINE_STAGE_LABELS[$newStage] ?? $newStage),
            null,
            ['old_stage' => $oldStage, 'new_stage' => $newStage]
        );
    }

    /**
     * Move a client to the next pipeline stage with gate validation.
     */
    public function advanceClientStage(Client $client, string $newStage): bool
    {
        $oldStage = $client->pipeline_stage;

        // Gate condition checks
        if ($newStage === 'training') {
            // Migration must be completed (or skipped if no previous software)
            $migration = $client->migration;
            if ($migration && $migration->status !== 'completed') {
                return false; // Block: migration incomplete
            }
        }

        if ($newStage === 'billing_active') {
            // Training must have started
            $training = $client->activeTraining;
            if (!$training) {
                return false; // Block: no training
            }
        }

        if ($newStage === 'active') {
            // Billing must be activated
            if ($client->activation_status !== 'active') {
                return false;
            }
        }

        $client->update(['pipeline_stage' => $newStage]);

        CrmActivity::log('client', $client->id, 'stage_change',
            "Stage: " . (Client::PIPELINE_STAGE_LABELS[$oldStage] ?? $oldStage) . " → " . (Client::PIPELINE_STAGE_LABELS[$newStage] ?? $newStage),
            null,
            ['old_stage' => $oldStage, 'new_stage' => $newStage]
        );

        return true;
    }

    /**
     * Complete a migration and advance client to training.
     */
    public function completeMigration(CrmMigration $migration): bool
    {
        // Validate: all critical tasks done
        if (!$migration->canComplete()) {
            return false;
        }

        // Validate: sign-off required
        if (!$migration->signoff_by) {
            return false;
        }

        $migration->update([
            'status'           => 'completed',
            'actual_end_date'  => now(),
            'old_system_status' => 'decommissioned',
            'progress_percent' => 100,
        ]);

        // Advance client to training
        $this->advanceClientStage($migration->client, 'training');

        CrmActivity::log('client', $migration->client_id, 'system',
            'Migration completed — Client advanced to Training',
            "Previous software: {$migration->previous_software_name}"
        );

        return true;
    }

    /**
     * Mark lead as lost.
     */
    public function markLeadLost(Lead $lead, string $reason): void
    {
        $lead->update([
            'status'         => 'lost',
            'pipeline_stage' => 'lost',
            'lost_reason'    => $reason,
            'lost_at'        => now(),
        ]);

        CrmActivity::log('lead', $lead->id, 'status_change',
            'Lead marked as Lost', $reason
        );
    }

    /**
     * Mark deal as lost.
     */
    public function markDealLost(Deal $deal, string $reason): void
    {
        $deal->update([
            'stage'          => 'closed_lost',
            'pipeline_stage' => 'lost',
            'closed_at'      => now(),
            'probability'    => 0,
            'notes'          => $deal->notes . "\nLost Reason: {$reason}",
        ]);

        CrmActivity::log('deal', $deal->id, 'status_change',
            'Deal marked as Lost', $reason
        );
    }

    /**
     * Schedule a follow-up for any entity.
     */
    public function scheduleFollowUp(
        string $entityType,
        int $entityId,
        string $type,
        string $title,
        \DateTime $scheduledAt,
        ?int $assignedTo = null,
        string $priority = 'medium',
        ?string $description = null,
    ): CrmFollowUp {
        $followUp = CrmFollowUp::create([
            'entity_type'  => $entityType,
            'entity_id'    => $entityId,
            'assigned_to'  => $assignedTo ?? auth()->id(),
            'type'         => $type,
            'title'        => $title,
            'description'  => $description,
            'scheduled_at' => $scheduledAt,
            'status'       => 'pending',
            'priority'     => $priority,
            'created_by'   => auth()->id(),
        ]);

        CrmActivity::log($entityType, $entityId, 'follow_up',
            "Follow-up scheduled: {$title}",
            "Type: {$type}, Date: " . $scheduledAt->format('Y-m-d H:i')
        );

        return $followUp;
    }

    /**
     * Get unified pipeline stats for dashboard.
     */
    public function getPipelineStats(): array
    {
        return [
            'total_leads'      => Lead::active()->count(),
            'new_leads'        => Lead::byPipelineStage('new_lead')->count(),
            'contacted_leads'  => Lead::byPipelineStage('contacted')->count(),
            'qualified_leads'  => Lead::byPipelineStage('qualified')->count(),
            'active_deals'     => Deal::active()->count(),
            'deal_value'       => Deal::active()->sum('value'),
            'won_deals'        => Deal::won()->count(),
            'won_value'        => Deal::won()->sum('value'),
            'total_clients'    => Client::active()->count(),
            'live_clients'     => Client::live()->count(),
            'pending_invoices' => \App\Models\CrmInvoice::unpaid()->count(),
            'overdue_invoices' => \App\Models\CrmInvoice::overdue()->count(),
            'open_tickets'     => \App\Models\SupportTicket::unresolved()->count(),
            'followups_today'  => CrmFollowUp::today()->pending()->count(),
            'followups_overdue' => CrmFollowUp::overdue()->count(),
            'active_migrations' => CrmMigration::active()->count(),
        ];
    }
}
