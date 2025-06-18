<?php

namespace App\Filament\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use App\Models\Section as SectionModel;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\SectionResource\Pages;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Resources\SectionResource\Pages\CreateSectionLesson;
use App\Filament\Resources\SectionResource\Pages\ManageSectionLessons;
use App\Filament\Resources\SectionResource\RelationManagers\LessonsRelationManager;

class SectionResource extends Resource
{
    use NestedResource;

    protected static ?string $model = SectionModel::class;

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
                    Toggle::make('status')
                        ->required(),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable(),
                IconColumn::make('status')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Action::make('lessons')
                    ->icon('heroicon-o-rectangle-stack')
                    ->url(fn(SectionModel $record): string => route('filament.admin.resources.sections.lessons', ['record' => $record->id])),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSections::route('/'),
            // 'create' => Pages\CreateSection::route('/create'),
            'view' => Pages\ViewSection::route('/{record}'),
            'edit' => Pages\EditSection::route('/{record}/edit'),

            'lessons' => ManageSectionLessons::route('/{record}/lessons'),
            'lessons.create' => CreateSectionLesson::route('/{record}/lessons/create'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewSection::class,
            Pages\EditSection::class,
            Pages\ManageSectionLessons::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'sections', // Relationship name
            'course', // Inverse relationship name
        );
    }
}
