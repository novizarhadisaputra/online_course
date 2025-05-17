<?php

namespace App\Filament\Resources\LessonResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class QuizzesRelationManager extends RelationManager
{
    use NestedRelationManager;

    protected static string $relationship = 'quizzes';

    public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->is_quiz;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([])
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('text')
            ->columns([
                Tables\Columns\TextColumn::make('text'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
