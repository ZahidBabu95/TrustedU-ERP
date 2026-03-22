<?php

namespace App\Filament\Resources\ChatbotKnowledge\Pages;

use App\Filament\Resources\ChatbotKnowledge\ChatbotKnowledgeResource;
use App\Models\ChatbotKnowledgeBase;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListChatbotKnowledge extends ListRecords
{
    protected static string $resource = ChatbotKnowledgeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('seedDefaults')
                ->label('Load Defaults')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Load Default Knowledge Base?')
                ->modalDescription('This will add default Q&A entries without removing existing ones.')
                ->action(function () {
                    ChatbotKnowledgeBase::seedDefaults();
                    $this->dispatch('$refresh');
                }),
            CreateAction::make()
                ->label('Add Knowledge Entry'),
        ];
    }
}
