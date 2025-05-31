<?php

namespace App\Filament\Clusters\Branches\Resources\BranchResource\Pages;

use App\Filament\Clusters\Branches\Resources\BranchResource;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateBranchStock extends CreateRelatedRecord
{
    use NestedPage;

    protected static string $relationship = 'stocks';

    protected static string $resource = BranchResource::class;

}
