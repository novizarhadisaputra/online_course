<?php

namespace App\Filament\Resources;

use App\Models\Quiz;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Repeater;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\QuizResource\Pages;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\QuizResource\Pages\CreateQuizOption;
use App\Filament\Resources\QuizResource\Pages\ManageQuizOption;
use Filament\Forms\Components\Toggle;

class QuizResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Quiz::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getBreadcrumbRecordLabel(Model $record)
    {
        return $record->text;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('text')
                        ->required()
                        ->maxLength(255),
                    Repeater::make('options')
                        ->relationship()
                        ->schema([
                            TextInput::make('option')
                                ->required()
                                ->maxLength(255),
                            TextInput::make('text')
                                ->required()
                                ->maxLength(255),
                            Toggle::make('is_correct')->default(false),
                        ])->grid(columns: 2)
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('model.name')
                    ->searchable(),
                IconColumn::make('status')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            // OptionsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuizzes::route('/'),
            // 'create' => Pages\CreateQuiz::route('/create'),
            'view' => Pages\ViewQuiz::route('/{record}'),
            'edit' => Pages\EditQuiz::route('/{record}/edit'),

            'options' => ManageQuizOption::route('/{record}/options'),
            'options.create' => CreateQuizOption::route('/{record}/options/create'),

        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'quizzes', // Relationship name
            'model', // Inverse relationship name
        );
    }
}
