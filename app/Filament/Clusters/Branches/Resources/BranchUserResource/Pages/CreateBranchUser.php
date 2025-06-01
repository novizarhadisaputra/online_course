<?php

namespace App\Filament\Clusters\Branches\Resources\BranchUserResource\Pages;

use App\Filament\Clusters\Branches\Resources\BranchUserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateBranchUser extends CreateRecord
{
    use NestedPage;

    protected static string $resource = BranchUserResource::class;
}
