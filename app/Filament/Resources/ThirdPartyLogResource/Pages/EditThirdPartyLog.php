<?php

namespace App\Filament\Resources\ThirdPartyLogResource\Pages;

use App\Filament\Resources\ThirdPartyLogResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditThirdPartyLog extends EditRecord
{
    protected static string $resource = ThirdPartyLogResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
