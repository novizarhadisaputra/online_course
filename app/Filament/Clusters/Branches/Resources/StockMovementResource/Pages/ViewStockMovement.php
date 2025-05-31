<?php

namespace App\Filament\Clusters\Branches\Resources\StockMovementResource\Pages;

use App\Filament\Clusters\Branches\Resources\StockMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewStockMovement extends ViewRecord
{
    use NestedPage;

    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
