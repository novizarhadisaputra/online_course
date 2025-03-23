<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Lesson;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\LessonResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\LessonResource\RelationManagers;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
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
                        ->required(),
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255),
                    TextInput::make('short_description')
                        ->maxLength(255)
                        ->default(null),
                    Textarea::make('description')
                        ->columnSpanFull()
                        ->required(),
                    Toggle::make('is_paid')
                        ->default(true),
                    Toggle::make('status')
                        ->required(),
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
            CommentsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            // 'index' => Pages\ListLessons::route('/'),
            // 'create' => Pages\CreateLesson::route('/create'),
            'view' => Pages\ViewLesson::route('/{record}'),
            'edit' => Pages\EditLesson::route('/{record}/edit'),
        ];
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
