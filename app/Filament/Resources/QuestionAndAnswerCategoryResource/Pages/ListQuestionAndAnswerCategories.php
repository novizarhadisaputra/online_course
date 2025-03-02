<?php

namespace App\Filament\Resources\QuestionAndAnswerCategoryResource\Pages;

use App\Filament\Resources\QuestionAndAnswerCategoryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListQuestionAndAnswerCategories extends ListRecords
{
    protected static string $resource = QuestionAndAnswerCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
