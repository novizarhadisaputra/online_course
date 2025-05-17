<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewQuiz extends ViewRecord
{
    use NestedPage;

    protected static string $resource = QuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
