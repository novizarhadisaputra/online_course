<?php

namespace App\Filament\Resources\SectionResource\Pages;

use App\Filament\Resources\CourseResource;
use App\Filament\Resources\SectionResource;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateSectionLesson extends CreateRelatedRecord
{
    use NestedPage;

    // This page also needs to know the ancestor relationship used (just like relation managers):
    protected static string $relationship = 'lessons';

    // We can usually guess the nested resource, but if your app has multiple resources for this
    // model, you will need to explicitly define it
    public static string $resource = SectionResource::class;
}
