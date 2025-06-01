<?php

namespace App\Filament\Clusters\Branches\Resources\BranchUserResource\Pages;

use App\Filament\Clusters\Branches\Resources\BranchUserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListBranchUsers extends ListRecords
{
    use NestedPage;

    protected static string $resource = BranchUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
