<?php

namespace App\Filament\Resources\ConfigAppResource\Pages;

use App\Filament\Resources\ConfigAppResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListConfigApps extends ListRecords
{
    protected static string $resource = ConfigAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
