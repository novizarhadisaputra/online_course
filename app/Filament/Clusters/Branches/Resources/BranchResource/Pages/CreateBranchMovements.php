<?php

namespace App\Filament\Clusters\Branches\Resources\BranchResource\Pages;

use App\Filament\Clusters\Branches\Resources\BranchResource;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateBranchMovements extends CreateRelatedRecord
{
    use NestedPage;

    protected static string $resource = BranchResource::class;

    // This page also needs to know the ancestor relationship used (just like relation managers):
    protected static string $relationship = 'stock_movements';
}
