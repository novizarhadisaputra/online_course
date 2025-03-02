<?php

namespace App\Filament\Resources\QuestionAndAnswerCategoryResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\QuestionAndAnswerCategoryResource;

class EditQuestionAndAnswerCategory extends EditRecord
{
    protected static string $resource = QuestionAndAnswerCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
