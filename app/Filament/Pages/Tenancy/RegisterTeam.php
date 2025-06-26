<?php

namespace App\Filament\Pages\Tenancy;

use App\Models\Branch;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Pages\Tenancy\RegisterTenant;
use App\Filament\Clusters\Branches\Resources\BranchResource;

class RegisterTeam extends RegisterTenant
{
    public static function getLabel(): string
    {
        return 'Register branch';
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                    if (($get('slug') ?? '') !== Str::slug($old)) {
                        return;
                    }
                    $set('slug', Str::slug($state));
                })
                ->unique(ignoreRecord: true)
                ->maxLength(255),
            TextInput::make('slug')
                ->readOnly()
                ->required(),
            RichEditor::make('description')
                ->fileAttachmentsDisk('s3')
                ->fileAttachmentsDirectory('attachments')
                ->fileAttachmentsVisibility('private'),
            Toggle::make('status')
                ->required(),
        ]);
    }

    protected function handleRegistration(array $data): Branch
    {
        DB::beginTransaction();
        try {
            $data['code'] = Str::upper(Str::random(10));
            $branch_code = Branch::where('code', $data['code'])->select(['id', 'code'])->first();
            while ($branch_code) {
                $data['code'] = Str::upper(Str::random(10));
                $branch_code = Branch::where('code', $data['code'])->select(['id', 'code'])->first();
            }

            $branch = Branch::create($data);
            $branch->users()->attach(auth()->user());

            DB::commit();
            return $branch;
        } catch (\Throwable $th) {
            DB::rollback();
            throw $th;
        }
    }
}
