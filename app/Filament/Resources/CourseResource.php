<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Form;
use App\Enums\CourseLevel;
use Filament\Tables\Table;
use App\Enums\TransactionStatus;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Pages\SubNavigationPosition;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\CourseResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Resources\CourseResource\Widgets\StatsOverview;
use App\Filament\Resources\CourseResource\Pages\CreateCourseSection;
use App\Filament\Resources\CourseResource\Pages\ManageCourseSections;
use App\Filament\Resources\CourseResource\RelationManagers\TagsRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\PricesRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\ReviewsRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\SectionsRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\TransactionsRelationManager;

class CourseResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'fas-graduation-cap';

    protected static ?string $navigationGroup = 'Master Data';

    public static function getBreadcrumbRecordLabel(Model $record)
    {
        return $record->name;
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::select('*');
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        };

        return $query->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('images')
                        ->required(),
                    TextInput::make('name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(150),
                    TextInput::make('short_description')
                        ->maxLength(255)
                        ->default(null),
                    Textarea::make('description')
                        ->columnSpanFull()
                        ->required(),
                    Textarea::make('requirement')
                        ->columnSpanFull()
                        ->required(),
                    Select::make('level')
                        ->options(CourseLevel::class),
                    TextInput::make('duration')
                        ->numeric()
                        ->minValue(1),
                    Select::make('tags')
                        ->multiple()
                        ->searchable()
                        ->relationship(titleAttribute: 'name'),
                    Select::make('language')
                        ->options([
                            'bahasa indonesia' => 'Bahasa Indonesia',
                            'english' => 'English',
                        ]),
                    Toggle::make('status')
                        ->required(),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {

        $query = Course::select('*');
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        };

        return $table
            ->query($query)
            ->columns([
                SpatieMediaLibraryImageColumn::make('image')->collection('images'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('user.name')
                    ->label('Instructor')
                    ->searchable(),
                TextColumn::make('sections_count')->counts([
                    'sections' => fn(Builder $query) => $query->where('sections.status', true),
                ]),
                TextColumn::make('lessons_count')->counts([
                    'lessons' => fn(Builder $query) => $query->where('lessons.status', true),
                ]),
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
                Action::make('sections')
                    ->icon('heroicon-o-rectangle-stack')
                    ->url(fn(Course $record): string => route('filament.admin.resources.courses.sections', ['record' => $record->id])),
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
            TransactionsRelationManager::make(['status' => TransactionStatus::SUCCESS]),
            SectionsRelationManager::make(),
            PricesRelationManager::make(),
            ReviewsRelationManager::make(),
            CommentsRelationManager::make(),
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCourses::route('/'),
            'create' => Pages\CreateCourse::route('/create'),
            'view' => Pages\ViewCourse::route('/{record}'),
            'edit' => Pages\EditCourse::route('/{record}/edit'),

            'sections' => ManageCourseSections::route('/{record}/sections'),
            'sections.create' => CreateCourseSection::route('/{record}/sections/create'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            StatsOverview::class,
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}
