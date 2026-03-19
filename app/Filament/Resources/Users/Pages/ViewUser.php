<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),

            // Login As Button
            Action::make('impersonate')
                ->label('Login As')
                ->icon('heroicon-o-arrow-right-on-rectangle')
                ->color('warning')
                ->visible(fn () =>
                    Auth::user()?->isSuperAdmin()
                    && $this->record->id !== Auth::id()
                    && !$this->record->isSuperAdmin()
                )
                ->requiresConfirmation()
                ->modalHeading('Login as ' . ($this->record->name ?? 'User') . '?')
                ->modalDescription('You will be logged in as this user. Use "Back to Super Admin" bar to return.')
                ->modalSubmitActionLabel('Login As This User')
                ->action(function () {
                    session()->put('impersonator_id', Auth::id());
                    session()->put('impersonator_name', Auth::user()->name);
                    Auth::login($this->record);
                    $this->redirect('/admin');
                }),
        ];
    }
}
