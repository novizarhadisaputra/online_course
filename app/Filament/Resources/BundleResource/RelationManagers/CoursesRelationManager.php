<?php

namespace App\Filament\Resources\BundleResource\RelationManagers;

use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                TextColumn::make('name'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\AttachAction::make()->form([
                    Select::make('course_id')
                        ->label(label: 'Courses')
                        ->options(Course::whereNotIn('id', $this->ownerRecord->courses()->select('id')->pluck('id'))->select('name', 'id')->pluck('name', 'id'))
                        ->searchable()
                        ->preload()
                        ->required(),

                ])->action(function (array $data): void {
                    $this->ownerRecord->courses()->attach($data['course_id']);
                }),
            ])
            ->actions([
                Tables\Actions\DetachAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                ]),
            ]);
    }
}
