<?php

namespace App\Filament\Clusters\Branches\Resources\BranchResource\Pages;

use App\Filament\Clusters\Branches\Resources\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ViewBranch extends ViewRecord
{
    use NestedPage;
    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}
