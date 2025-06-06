<?php

namespace App\Filament\Clusters\Products\Resources\ProductResource\Pages;

use App\Filament\Clusters\Products\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewProduct extends ViewRecord
{
    use NestedPage;

    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
