<?php

namespace App\Filament\Resources\CourseResource\Pages;

use App\Models\Enrollment;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\CourseResource;
use Filament\Resources\Pages\ManageRelatedRecords;

class ManageCourseStudents extends ManageRelatedRecords
{
    protected static string $resource = CourseResource::class;

    protected static string $relationship = 'enrollments';

    protected static ?string $navigationIcon = '';

    public static function getNavigationLabel(): string
    {
        return 'Students';
    }

    public function getBreadcrumbs(): array
    {
        return [
            route('filament.admin.resources.courses.index') => Str::ucfirst($this->record->getTable()),
            route('filament.admin.resources.courses.view', ['record' => $this->record]) => $this->record->name,
            'students' => 'Students'
        ];
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->description(fn(Enrollment $record): string => $record->user->email)
                    ->searchable(),
                TextColumn::make('score.batches')
                    ->label('Batches')
                    ->sortable(),
                TextColumn::make('score.value')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([
                ActionGroup::make([
                    Action::make('assign_score')
                        ->icon('heroicon-o-pencil-square')
                        ->fillForm(fn(Enrollment $record): array => [
                            'value' => $record->score ? $record->score->value : 0,
                            'batches' => $record->score ? $record->score->batches : 1,
                            'is_graduated' => $record->score ? $record->score->is_graduated : true,
                        ])->form([
                            TextInput::make('value')
                                ->numeric(),
                            TextInput::make('batches')
                                ->numeric(),
                            Toggle::make('is_graduated'),
                        ])
                        ->action(function (array $data, Enrollment $record): void {
                            $score = $record->score()->where('user_id', $record->user_id)->first();
                            if (!$score) {
                                $score = $record->score()->create([
                                    'batches' => 1,
                                    'value' => $data['value'],
                                    'is_graduated' => $data['is_graduated'],
                                    'user_id' => $record->user_id,
                                ]);
                            }
                            $score->batches = 1;
                            $score->value = $data['value'];
                            $score->is_graduated = $data['is_graduated'];
                            $score->user_id = $record->user_id;
                            $score->save();

                            $record->save();
                        })->slideOver()
                ])
            ])
            ->bulkActions([]);
    }
}
