<?php

namespace App\Filament\Resources\QuestionAndAnswerResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\QuestionAndAnswerResource;

class CreateQuestionAndAnswer extends CreateRecord
{
    protected static string $resource = QuestionAndAnswerResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
