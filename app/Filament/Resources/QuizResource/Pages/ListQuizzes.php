<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListQuizzes extends ListRecords
{
    use NestedPage;

    protected static string $resource = QuizResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
