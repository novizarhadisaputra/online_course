<?php

namespace App\Filament\Clusters\Branches\Resources\BranchUserResource\Pages;

use App\Filament\Clusters\Branches\Resources\BranchUserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditBranchUser extends EditRecord
{
    use NestedPage;

    protected static string $resource = BranchUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }
}
