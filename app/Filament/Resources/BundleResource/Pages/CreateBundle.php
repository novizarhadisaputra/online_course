<?php

namespace App\Filament\Resources\BundleResource\Pages;

use Illuminate\Support\Str;
use App\Filament\Resources\BundleResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBundle extends CreateRecord
{
    protected static string $resource = BundleResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
