<?php

namespace App\Filament\Resources\ThirdPartyLogResource\Pages;

use App\Filament\Resources\ThirdPartyLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListThirdPartyLogs extends ListRecords
{
    protected static string $resource = ThirdPartyLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
