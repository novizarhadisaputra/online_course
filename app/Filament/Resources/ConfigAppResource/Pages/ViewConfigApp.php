<?php

namespace App\Filament\Resources\ConfigAppResource\Pages;

use App\Filament\Resources\ConfigAppResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewConfigApp extends ViewRecord
{
    protected static string $resource = ConfigAppResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
