<?php

namespace App\Filament\Clusters\Branches\Resources\StockResource\Pages;

use App\Filament\Clusters\Branches\Resources\StockResource;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateStock extends CreateRecord
{
    use NestedPage;

    protected static string $resource = StockResource::class;
}
