<?php

namespace App\Filament\Clusters\Branches\Resources\StockMovementResource\Pages;

use App\Filament\Clusters\Branches\Resources\StockMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListStockMovements extends ListRecords
{
    use NestedPage;

    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
