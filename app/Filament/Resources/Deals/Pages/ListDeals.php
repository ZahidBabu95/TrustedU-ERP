<?php

namespace App\Filament\Resources\Deals\Pages;

use App\Filament\Resources\Deals\DealResource;
use App\Models\Deal;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListDeals extends ListRecords
{
    protected static string $resource = DealResource::class;
    protected string $view = 'filament.resources.deals.pages.list-deals';

    public function getDealsByStage(): array
    {
        $query = DealResource::getEloquentQuery();

        $grouped = [];
        foreach (Deal::KANBAN_STAGES as $stage) {
            $grouped[$stage] = $query->clone()
                ->where('stage', $stage)
                ->orderBy('sort_order')
                ->orderByDesc('created_at')
                ->with(['assignee', 'client', 'lead'])
                ->get();
        }
        return $grouped;
    }

    public function getKanbanStats(): array
    {
        $query = DealResource::getEloquentQuery();

        return [
            'total_value'    => $query->clone()->whereNotIn('stage', ['closed_won', 'closed_lost'])->sum('value'),
            'active_deals'   => $query->clone()->whereNotIn('stage', ['closed_won', 'closed_lost'])->count(),
            'won_value'      => $query->clone()->where('stage', 'closed_won')->sum('value'),
            'won_count'      => $query->clone()->where('stage', 'closed_won')->count(),
            'lost_count'     => $query->clone()->where('stage', 'closed_lost')->count(),
            'weighted_value' => $query->clone()->whereNotIn('stage', ['closed_won', 'closed_lost'])
                                    ->selectRaw('SUM(value * probability / 100) as weighted')
                                    ->value('weighted') ?? 0,
        ];
    }

    public function updateDealStage(int $dealId, string $newStage): void
    {
        $deal = Deal::findOrFail($dealId);
        $updates = ['stage' => $newStage];

        // Auto-set probability based on stage
        if (isset(Deal::STAGE_PROBABILITIES[$newStage])) {
            $updates['probability'] = Deal::STAGE_PROBABILITIES[$newStage];
        }

        // If closing, set closed_at
        if (in_array($newStage, ['closed_won', 'closed_lost'])) {
            $updates['closed_at'] = now();
        }

        $deal->update($updates);
        $this->dispatch('$refresh');
    }

    /**
     * Convert a deal to a client directly from Kanban.
     */
    public function convertDealToClient(int $dealId): void
    {
        $deal = Deal::findOrFail($dealId);

        if ($deal->isConverted()) {
            Notification::make()
                ->title('Already Converted')
                ->body('This deal has already been converted to a client.')
                ->warning()
                ->send();
            return;
        }

        $client = $deal->convertToClient($deal->team_id);

        Notification::make()
            ->title('🎉 Client Created!')
            ->body("Client \"{$client->name}\" has been created from deal \"{$deal->title}\".")
            ->success()
            ->send();

        $this->dispatch('$refresh');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
