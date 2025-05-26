<?php

namespace App\Filament\Resources\QuestionAndAnswerResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\QuestionAndAnswerResource;

class EditQuestionAndAnswer extends EditRecord
{
    protected static string $resource = QuestionAndAnswerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['slug'] = Str::slug($data['question']);

        return $data;
    }
}
