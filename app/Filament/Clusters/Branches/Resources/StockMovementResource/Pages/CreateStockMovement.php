<?php

namespace App\Filament\Clusters\Branches\Resources\StockMovementResource\Pages;

use App\Filament\Clusters\Branches\Resources\StockMovementResource;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateStockMovement extends CreateRecord
{
    use NestedPage;

    protected static string $resource = StockMovementResource::class;
}
