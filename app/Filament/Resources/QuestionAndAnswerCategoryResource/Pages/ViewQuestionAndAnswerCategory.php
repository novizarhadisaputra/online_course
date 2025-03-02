<?php

namespace App\Filament\Resources\QuestionAndAnswerCategoryResource\Pages;

use App\Filament\Resources\QuestionAndAnswerCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewQuestionAndAnswerCategory extends ViewRecord
{
    protected static string $resource = QuestionAndAnswerCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
