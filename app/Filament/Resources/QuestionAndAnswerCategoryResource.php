<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use App\Models\QuestionAndAnswerCategory;
use Filament\Forms\Components\RichEditor;
use App\Filament\Resources\QuestionAndAnswerCategoryResource\Pages;

class QuestionAndAnswerCategoryResource extends Resource
{
    protected static ?string $model = QuestionAndAnswerCategory::class;

    protected static ?string $navigationIcon = '';

    protected static ?string $navigationGroup = 'Master Data';

    protected static ?string $label = 'Categories';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    RichEditor::make('description')
                        ->fileAttachmentsDisk('s3_public')
                        ->fileAttachmentsDirectory('attachments'),
                    Toggle::make('status')
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID')
                    ->searchable(),
                TextColumn::make('name')
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
            'index' => Pages\ListQuestionAndAnswerCategories::route('/'),
            'create' => Pages\CreateQuestionAndAnswerCategory::route('/create'),
            'view' => Pages\ViewQuestionAndAnswerCategory::route('/{record}'),
            'edit' => Pages\EditQuestionAndAnswerCategory::route('/{record}/edit'),
        ];
    }
}
