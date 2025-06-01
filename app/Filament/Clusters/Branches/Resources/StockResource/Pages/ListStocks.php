<?php

namespace App\Filament\Clusters\Branches\Resources\StockResource\Pages;

use App\Filament\Clusters\Branches\Resources\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListStocks extends ListRecords
{
    use NestedPage;

    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
