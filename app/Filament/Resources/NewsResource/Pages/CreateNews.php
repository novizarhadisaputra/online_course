<?php

namespace App\Filament\Resources\NewsResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\NewsResource;
use Filament\Resources\Pages\CreateRecord;

class CreateNews extends CreateRecord
{
    protected static string $resource = NewsResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $data;
    }
}
