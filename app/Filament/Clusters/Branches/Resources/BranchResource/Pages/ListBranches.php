<?php

namespace App\Filament\Clusters\Branches\Resources\BranchResource\Pages;

use App\Filament\Clusters\Branches\Resources\BranchResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListBranches extends ListRecords
{
    use NestedPage;

    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
