<?php

namespace App\Filament\Resources\Team\Pages;

use App\Filament\Resources\Team\TeamResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTeamMember extends EditRecord
{
    protected static string $resource = TeamResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->record;

        // Load profile data
        if ($record->profile) {
            foreach ($record->profile->toArray() as $key => $value) {
                if (!in_array($key, ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at'])) {
                    $data["profile.{$key}"] = $value;
                }
            }
        }

        // Load financial data
        if ($record->financial) {
            foreach ($record->financial->toArray() as $key => $value) {
                if (!in_array($key, ['id', 'user_id', 'created_at', 'updated_at', 'deleted_at'])) {
                    $data["financial.{$key}"] = $value;
                }
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $record = $this->record;

        // Update or create profile
        $profileData = collect($this->data)->filter(fn($v, $k) => str_starts_with($k, 'profile.'))->mapWithKeys(fn($v, $k) => [str_replace('profile.', '', $k) => $v])->toArray();
        if (!empty($profileData)) {
            $record->profile()->updateOrCreate(['user_id' => $record->id], $profileData);
        }

        // Update or create financial
        $financialData = collect($this->data)->filter(fn($v, $k) => str_starts_with($k, 'financial.'))->mapWithKeys(fn($v, $k) => [str_replace('financial.', '', $k) => $v])->toArray();
        if (!empty($financialData)) {
            $record->financial()->updateOrCreate(['user_id' => $record->id], $financialData);
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
