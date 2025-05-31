<?php

namespace App\Filament\Clusters\Products\Resources\ProductResource\Pages;

use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Products\Resources\ProductResource;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateProduct extends CreateRecord
{
    use NestedPage;

    protected static string $resource = ProductResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
