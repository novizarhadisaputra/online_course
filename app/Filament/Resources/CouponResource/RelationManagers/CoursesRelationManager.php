<?php

namespace App\Filament\Resources\CouponResource\RelationManagers;

use App\Enums\CouponType;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->type === CouponType::SPECIFIC_COURSE->value ? true : false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // ...
                Tables\Actions\AttachAction::make()->multiple(),
            ])
            ->actions([
                // ...
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // ...
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
