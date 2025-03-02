<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Models\QuestionAndAnswer;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Filament\Resources\QuestionAndAnswerResource\Pages;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;

class QuestionAndAnswerResource extends Resource
{
    protected static ?string $model = QuestionAndAnswer::class;

    protected static ?string $navigationIcon = 'heroicon-o-question-mark-circle';

    protected static ?string $navigationGroup = 'Master Data';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('question')
                        ->required()
                        ->maxLength(255),
                    Textarea::make('answer')
                        ->required()
                        ->columnSpanFull(),
                    Select::make('question_and_answer_category_id')
                        ->label('Category')
                        ->relationship(name: 'category', titleAttribute: 'name')
                        ->required(),
                    Toggle::make('status')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('question')
                    ->searchable(),
                TextColumn::make('category.name')
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListQuestionAndAnswers::route('/'),
            'create' => Pages\CreateQuestionAndAnswer::route('/create'),
            'view' => Pages\ViewQuestionAndAnswer::route('/{record}'),
            'edit' => Pages\EditQuestionAndAnswer::route('/{record}/edit'),
        ];
    }
}
