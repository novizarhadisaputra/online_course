<?php

namespace App\Filament\Resources\OptionResource\Pages;

use App\Filament\Resources\OptionResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewOption extends ViewRecord
{
    use NestedPage;

    protected static string $resource = OptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
