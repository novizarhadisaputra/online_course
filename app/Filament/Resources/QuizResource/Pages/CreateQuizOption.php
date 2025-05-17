<?php

namespace App\Filament\Resources\QuizResource\Pages;

use App\Filament\Resources\QuizResource;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateQuizOption extends CreateRelatedRecord
{
    use NestedPage;

    // This page also needs to know the ancestor relationship used (just like relation managers):
    protected static string $relationship = 'quizzes';

    // We can usually guess the nested resource, but if your app has multiple resources for this
    // model, you will need to explicitly define it
    public static string $resource = QuizResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
