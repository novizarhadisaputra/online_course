<?php

namespace App\Filament\Clusters\Branches\Resources\StockResource\Pages;

use App\Filament\Clusters\Branches\Resources\StockResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditStock extends EditRecord
{
    use NestedPage;
    protected static string $resource = StockResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
