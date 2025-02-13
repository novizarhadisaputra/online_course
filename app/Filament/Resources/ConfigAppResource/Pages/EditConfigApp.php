<?php

namespace App\Filament\Resources\ConfigAppResource\Pages;

use App\Filament\Resources\ConfigAppResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditConfigApp extends EditRecord
{
    protected static string $resource = ConfigAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
