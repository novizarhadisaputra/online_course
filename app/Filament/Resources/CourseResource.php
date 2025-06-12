<?php

namespace App\Filament\Resources;

use Filament\Tables;
use App\Models\Course;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\Page;
use App\Enums\CourseLevel;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use App\Enums\TransactionStatus;
use Filament\Resources\Resource;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use Guava\FilamentNestedResources\Ancestor;
use App\Filament\Resources\CourseResource\Pages;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Hugomyb\FilamentMediaAction\Tables\Actions\MediaAction;
use App\Filament\Resources\CourseResource\Widgets\StatsOverview;
use App\Filament\Resources\CourseResource\Pages\CreateCourseSection;
use App\Filament\Resources\CourseResource\Pages\ManageCourseSections;
use App\Filament\Resources\CourseResource\RelationManagers\PricesRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\ReviewsRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\TransactionsRelationManager;
use App\Filament\Resources\CourseResource\RelationManagers\LearningMethodsRelationManager;

class CourseResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Course::class;

    protected static ?string $navigationIcon = 'fas-graduation-cap';

    protected static ?string $navigationGroup = 'Master Data';

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'User' => $record->user->name,
            'Category' => $record->category->name,
        ];
    }

    public static function getBreadcrumbRecordLabel(Model $record)
    {
        return $record->name;
    }

    public static function getGlobalSearchEloquentQuery(): Builder
    {
        return parent::getGlobalSearchEloquentQuery()->with(['user', 'category']);
    }

    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::select('*');
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        };

        return $query->count();
    }

    public static function getSelectCategories(): Select
    {
        return Select::make('category_id')
            ->searchable()
            ->relationship(titleAttribute: 'name', name: 'category')
            ->createOptionForm([
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('images')
                    ->visibility('private')
                    ->disk('s3'),
                Grid::make()->schema([
                    TextInput::make('name')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),
                    TextInput::make('slug')
                        ->readOnly()
                        ->required(),
                ]),
                RichEditor::make('description')
                    ->fileAttachmentsDisk('s3')
                    ->fileAttachmentsDirectory('attachments')
                    ->fileAttachmentsVisibility('private'),
                Toggle::make('status')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function getSelectCompetences(): Select
    {
        return Select::make('competences')
            ->multiple()
            ->searchable()
            ->relationship(titleAttribute: 'name')
            ->createOptionForm([
                Grid::make()->schema([
                    TextInput::make('name')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),
                    TextInput::make('slug')
                        ->readOnly()
                        ->required(),
                ]),
                TextInput::make('short_description')
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function getSelectTags(): Select
    {
        return Select::make('tags')
            ->multiple()
            ->searchable()
            ->relationship(titleAttribute: 'name')
            ->createOptionForm([
                Grid::make()->schema([
                    TextInput::make('name')
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),
                    TextInput::make('slug')
                        ->readOnly()
                        ->required(),
                ]),
                TextInput::make('short_description')
                    ->maxLength(255),
                Textarea::make('description')
                    ->columnSpanFull(),
                Toggle::make('status')
                    ->default(true)
                    ->required(),
            ]);
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->collection('images')
                        ->visibility('private')
                        ->disk('s3')
                        ->image()
                        ->previewable()
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('thumbnail')
                        ->collection('thumbnails')
                        ->visibility('private')
                        ->disk('s3')
                        ->image()
                        ->previewable()
                        ->required(),
                    SpatieMediaLibraryFileUpload::make('preview')
                        ->collection('previews')
                        ->visibility('private')
                        ->disk('s3')
                        ->acceptedFileTypes(['video/*'])
                        ->required(),
                    TextInput::make('name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(150),
                    TextInput::make('short_description')
                        ->maxLength(255)
                        ->default(null),
                    RichEditor::make('description')
                        ->fileAttachmentsDisk('s3')
                        ->fileAttachmentsDirectory('attachments')
                        ->fileAttachmentsVisibility('private'),
                    Textarea::make('requirement')
                        ->columnSpanFull()
                        ->required(),
                    Select::make('level')
                        ->options(CourseLevel::class),
                    Grid::make()->schema([
                        TextInput::make('duration')
                            ->numeric()
                            ->minValue(1),
                        TextInput::make('duration_units')
                            ->default('minutes')
                            ->maxLength(255),
                    ]),
                    static::getSelectCategories(),
                    static::getSelectCompetences(),
                    static::getSelectTags(),
                    Select::make('language')
                        ->options([
                            'bahasa indonesia' => 'Bahasa Indonesia',
                            'english' => 'English',
                        ]),
                    KeyValue::make('meta')
                        ->default([
                            'title' => '',
                            'description' => ''
                        ]),
                    Grid::make()->schema([
                        Toggle::make('is_paid')
                            ->required(),
                        Toggle::make('status')
                            ->required(),
                    ])
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
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('images')
                    ->visibility('private')
                    ->disk('s3'),
                TextColumn::make('name')
                    ->description(fn(Course $record): string | null => $record->short_description)
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
                MediaAction::make('preview')
                    ->icon(icon: 'heroicon-s-video-camera')
                    ->media(fn($record) => $record->hasMedia('previews') ? $record->getMedia('previews')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null)
                    ->visible(fn($record) => $record->hasMedia('previews')),
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
            LearningMethodsRelationManager::make(),
            PricesRelationManager::make(),
            TransactionsRelationManager::make(['status' => TransactionStatus::SUCCESS]),
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

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewCourse::class,
            Pages\EditCourse::class,
            Pages\ManageCourseSections::class,
        ]);
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
