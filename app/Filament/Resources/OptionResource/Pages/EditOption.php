<?php

namespace App\Filament\Resources\OptionResource\Pages;

use App\Filament\Resources\OptionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditOption extends EditRecord
{
    use NestedPage;

    protected static string $resource = OptionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
