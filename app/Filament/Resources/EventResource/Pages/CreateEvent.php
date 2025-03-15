<?php

namespace App\Filament\Resources\EventResource\Pages;

use Filament\Actions;
use Illuminate\Support\Str;
use App\Filament\Resources\EventResource;
use Filament\Resources\Pages\CreateRecord;

class CreateEvent extends CreateRecord
{
    protected static string $resource = EventResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['user_id'] = auth()->id();
        $data['slug'] = Str::slug($data['name']);

        return $data;
    }
}
