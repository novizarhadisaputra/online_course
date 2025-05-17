<?php

namespace App\Filament\Resources\OptionResource\Pages;

use App\Filament\Resources\OptionResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateOption extends CreateRecord
{
    use NestedPage;

    protected static string $resource = OptionResource::class;
}
