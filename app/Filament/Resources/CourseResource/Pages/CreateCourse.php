<?php

namespace App\Filament\Resources\CourseResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateCourse extends CreateRecord
{
    use NestedPage;

    protected static string $resource = CourseResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
