<?php

namespace App\Filament\Clusters\Branches\Resources\BranchResource\Pages;

use Filament\Actions;
use App\Models\Branch;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Clusters\Branches\Resources\BranchResource;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class EditBranch extends EditRecord
{
    use NestedPage;

    protected static string $resource = BranchResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ViewAction::make(),
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['slug'] = Str::slug($data['name']);

        $record->update($data);

        return $record;
    }
}
