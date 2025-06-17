<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Lesson;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\LessonResource\Pages;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\LessonResource\Pages\CreateLessonQuiz;
use App\Filament\Resources\LessonResource\Pages\ManageLessonQuiz;
use App\Filament\Resources\LessonResource\RelationManagers\EventsRelationManager;
use App\Filament\Resources\LessonResource\RelationManagers\CommentsRelationManager;

class LessonResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Lesson::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getBreadcrumbRecordLabel(Model $record)
    {
        return $record->name;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    SpatieMediaLibraryFileUpload::make('attachment')
                        ->collection('attachments')
                        ->helperText('PDF, Image or Video')
                        ->visibility('private')
                        ->disk('s3')
                        ->required(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('short_description')
                        ->maxLength(255)
                        ->default(null),
                    RichEditor::make('description')
                        ->fileAttachmentsDisk('s3')
                        ->fileAttachmentsDirectory('attachments')
                        ->fileAttachmentsVisibility('private')
                        ->required(),
                    Grid::make()->schema([
                        TextInput::make('duration')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        TextInput::make('duration_units')
                            ->default('minutes')
                            ->required()
                            ->maxLength(255),
                    ]),
                    Grid::make()->schema([
                        Toggle::make('is_paid')
                            ->default(true),
                        Toggle::make('is_quiz')
                            ->default(false),
                        Toggle::make('status')
                            ->required(),
                    ])->columns(3)
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                //
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
            EventsRelationManager::make(),
            CommentsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListLessons::route('/'),
            // 'create' => Pages\CreateLesson::route('/create'),
            'view' => Pages\ViewLesson::route('/{record}'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
            'quizzes' => ManageLessonQuiz::route('/{record}/quizzes'),
            'quizzes.create' => CreateLessonQuiz::route('/{record}/quizzes/create'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewLesson::class,
            Pages\EditLesson::class,
            Pages\ManageLessonQuiz::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'lessons', // Relationship name
            'section', // Inverse relationship name
        );
    }
}
