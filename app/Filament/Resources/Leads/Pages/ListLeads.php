<?php

namespace App\Filament\Resources\Leads\Pages;

use App\Filament\Resources\Leads\LeadResource;
use App\Models\Lead;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;
    protected string $view = 'filament.resources.leads.pages.list-leads';

    public string $viewMode = 'kanban';

    public function getLeadsByStatus(): array
    {
        $query = LeadResource::getEloquentQuery();

        $grouped = [];
        foreach (Lead::KANBAN_STATUSES as $status) {
            $grouped[$status] = $query->clone()
                ->where('status', $status)
                ->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->with('assignee')
                ->get();
        }
        return $grouped;
    }

    public function getKanbanStats(): array
    {
        $query = LeadResource::getEloquentQuery();

        return [
            'total_value'  => $query->clone()->whereNotIn('status', ['won', 'lost'])->sum('value'),
            'active_leads' => $query->clone()->whereNotIn('status', ['won', 'lost'])->count(),
            'won_count'    => $query->clone()->where('status', 'won')->count(),
            'lost_count'   => $query->clone()->where('status', 'lost')->count(),
        ];
    }

    public function updateLeadStatus(int $leadId, string $newStatus): void
    {
        $lead = Lead::findOrFail($leadId);
        $lead->update(['status' => $newStatus]);

        $this->dispatch('$refresh');
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = $mode;
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
