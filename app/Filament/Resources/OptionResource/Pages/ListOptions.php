<?php

namespace App\Filament\Resources\OptionResource\Pages;

use App\Filament\Resources\OptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListOptions extends ListRecords
{
    use NestedPage;

    protected static string $resource = OptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
