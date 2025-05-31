<?php

namespace App\Filament\Clusters\Branches\Resources\StockMovementResource\Pages;

use App\Filament\Clusters\Branches\Resources\StockMovementResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditStockMovement extends EditRecord
{
    use NestedPage;

    protected static string $resource = StockMovementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
