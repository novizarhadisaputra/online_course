<?php

namespace App\Filament\Resources\CourseResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use Filament\Resources\Pages\Page;
use Filament\Resources\Pages\EditRecord;
use Filament\Pages\SubNavigationPosition;
use App\Filament\Resources\CourseResource;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditCourse extends EditRecord
{
    use NestedPage;

    protected static string $resource = CourseResource::class;

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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            ViewCourse::class,
            ManageCourseSections::class,
        ]);
    }
}
