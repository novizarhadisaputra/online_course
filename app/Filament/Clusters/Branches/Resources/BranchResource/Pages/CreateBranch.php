<?php

namespace App\Filament\Clusters\Branches\Resources\BranchResource\Pages;

use Illuminate\Support\Str;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Clusters\Branches\Resources\BranchResource;
use App\Models\Branch;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreateBranch extends CreateRecord
{
    use NestedPage;

    protected static string $resource = BranchResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $slug = Str::slug($data['name']);

        $code = Str::upper(Str::random(10));
        $branch_code = Branch::where('code', $code)->select(['id', 'code'])->first();
        while ($branch_code) {
            $code = Str::upper(Str::random(10));
            $branch_code = Branch::where('code', $code)->select(['id', 'code'])->first();
        }

        $data['slug'] = $slug;
        $data['code'] = $code;

        return $data;
    }
}
