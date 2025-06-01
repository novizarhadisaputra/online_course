<?php

namespace App\Filament\Clusters\Branches\Resources\BranchUserResource\Pages;

use App\Filament\Clusters\Branches\Resources\BranchUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewBranchUser extends ViewRecord
{
    use NestedPage;

    protected static string $resource = BranchUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
