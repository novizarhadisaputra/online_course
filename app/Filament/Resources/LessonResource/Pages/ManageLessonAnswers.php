<?php

namespace App\Filament\Resources\LessonResource\Pages;

use App\Models\Enrollment;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Actions\ActionGroup;
use App\Filament\Resources\LessonResource;
use App\Models\Answer;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ManageRelatedRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageLessonAnswers extends ManageRelatedRecords
{
    use NestedPage;
    use NestedRelationManager;

    protected static string $resource = LessonResource::class;

    protected static string $relationship = 'answers';

    protected static ?string $navigationIcon = '';

    public static function getNavigationLabel(): string
    {
        return 'Manage Answers';
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('user.name')
            ->columns([
                TextColumn::make('user.name')
                    ->description(fn(Answer $record): string => $record->user->email)
                    ->searchable(),
                TextColumn::make('text'),
                TextColumn::make('model.score.batches')
                    ->label('Batches')
                    ->sortable(),
                TextColumn::make('model.score.value')
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
                        ->fillForm(fn(Answer $record): array => [
                            'answer' => $record->text,
                            'value' => $this->record->score ? $this->record->score->value : 0,
                            'batches' => $this->record->score ? $this->record->score->batches : 1,
                            'is_graduated' => $this->record->score ? $this->record->score->is_graduated : true,
                        ])->form([
                            Textarea::make('answer')->readOnly(),
                            TextInput::make('value')
                                ->numeric(),
                            TextInput::make('batches')
                                ->numeric(),
                            Toggle::make('is_graduated'),
                        ])
                        ->action(function (array $data, Answer $record): void {
                            $score = $this->record->score()->where('user_id', $record->user_id)->first();
                            if (!$score) {
                                $score = $this->record->score()->create([
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

                            dd($score);

                            $record->save();
                        })->slideOver()
                ])
            ])
            ->bulkActions([]);
    }
}
