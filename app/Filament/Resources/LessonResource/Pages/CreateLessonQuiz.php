<?php

namespace App\Filament\Resources\LessonResource\Pages;

use App\Filament\Resources\LessonResource;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateLessonQuiz extends CreateRelatedRecord
{
    use NestedPage;

    // This page also needs to know the ancestor relationship used (just like relation managers):
    protected static string $relationship = 'quizzes';

    // We can usually guess the nested resource, but if your app has multiple resources for this
    // model, you will need to explicitly define it
    public static string $resource = LessonResource::class;
}
