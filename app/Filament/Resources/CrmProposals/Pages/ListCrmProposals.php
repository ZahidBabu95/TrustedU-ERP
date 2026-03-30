<?php

namespace App\Filament\Resources\CrmProposals\Pages;

use App\Filament\Resources\CrmProposals\CrmProposalResource;
use Filament\Resources\Pages\ListRecords;

class ListCrmProposals extends ListRecords
{
    protected static string $resource = CrmProposalResource::class;
    protected static ?string $title = 'Proposals';
}
