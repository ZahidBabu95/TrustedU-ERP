<?php

namespace App\Filament\Resources\Team\Pages;

use App\Filament\Resources\Team\TeamResource;
use App\Models\EmployeeFinancial;
use App\Models\EmployeeProfile;
use Filament\Resources\Pages\CreateRecord;

class CreateTeamMember extends CreateRecord
{
    protected static string $resource = TeamResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Set defaults
        $data['is_active'] = $data['is_active'] ?? true;
        $data['role'] = $data['role'] ?? 'team_member';

        return $data;
    }

    protected function afterCreate(): void
    {
        $record = $this->record;

        // Create profile if profile data exists
        $profileData = collect($this->data)->filter(fn($v, $k) => str_starts_with($k, 'profile.'))->mapWithKeys(fn($v, $k) => [str_replace('profile.', '', $k) => $v])->toArray();
        if (array_filter($profileData)) {
            $record->profile()->create($profileData);
        } else {
            $record->profile()->create([]);
        }

        // Create financial record if data exists
        $financialData = collect($this->data)->filter(fn($v, $k) => str_starts_with($k, 'financial.'))->mapWithKeys(fn($v, $k) => [str_replace('financial.', '', $k) => $v])->toArray();
        if (array_filter($financialData)) {
            $record->financial()->create($financialData);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
