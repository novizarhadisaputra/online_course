<?php

namespace App\Filament\Resources\QuestionAndAnswerCategoryResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\QuestionAndAnswerCategoryResource;

class CreateQuestionAndAnswerCategory extends CreateRecord
{
    protected static string $resource = QuestionAndAnswerCategoryResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
