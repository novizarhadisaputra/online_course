<?php

namespace App\Filament\Resources\ThirdPartyLogResource\Pages;

use App\Filament\Resources\ThirdPartyLogResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewThirdPartyLog extends ViewRecord
{
    protected static string $resource = ThirdPartyLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
