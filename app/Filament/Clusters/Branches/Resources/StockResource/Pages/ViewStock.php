<?php

namespace App\Filament\Clusters\Branches\Resources\StockResource\Pages;

use App\Filament\Clusters\Branches\Resources\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewStock extends ViewRecord
{
    use NestedPage;

    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
