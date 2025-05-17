<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateQuiz extends CreateRecord
{
    use NestedPage;

    protected static string $resource = QuizResource::class;
}
