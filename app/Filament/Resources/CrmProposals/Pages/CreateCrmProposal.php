<?php

namespace App\Filament\Resources\CrmProposals\Pages;

use App\Filament\Resources\CrmProposals\CrmProposalResource;
use App\Models\CrmActivity;
use App\Models\CrmProposal;
use Filament\Resources\Pages\CreateRecord;

class CreateCrmProposal extends CreateRecord
{
    protected static string $resource = CrmProposalResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = auth()->id();

        // Auto-set version
        $maxVersion = CrmProposal::where('deal_id', $data['deal_id'])->max('version') ?? 0;
        $data['version'] = $maxVersion + 1;

        // Calculate final price if not set
        if (empty($data['final_price'])) {
            $basePrice   = floatval($data['base_price'] ?? 0);
            $discountPct = floatval($data['discount_percent'] ?? 0);
            $discountAmt = $discountPct > 0
                ? ($basePrice * $discountPct / 100)
                : floatval($data['discount_amount'] ?? 0);
            $data['discount_amount'] = $discountAmt;
            $data['final_price']     = max(0, $basePrice - $discountAmt);
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Update deal pipeline stage to proposal_draft
        $record->deal->update(['pipeline_stage' => 'proposal_draft']);

        // Log activity
        CrmActivity::log('deal', $record->deal_id, 'note',
            "Proposal v{$record->version} created: {$record->title}",
            "Base: ৳{$record->base_price}, Final: ৳{$record->final_price}",
            ['proposal_id' => $record->id]
        );
    }
}
