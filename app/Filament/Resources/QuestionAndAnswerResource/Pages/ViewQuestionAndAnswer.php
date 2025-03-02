<?php

namespace App\Filament\Resources\QuestionAndAnswerResource\Pages;

use App\Filament\Resources\QuestionAndAnswerResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionAndAnswer extends ViewRecord
{
    protected static string $resource = QuestionAndAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
