<?php

namespace App\Filament\Resources\LessonResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use App\Enums\MeetingType;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\LessonResource;
use Filament\Forms\Components\DateTimePicker;
use Filament\Resources\Pages\ManageRelatedRecords;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class ManageLessonEvent extends ManageRelatedRecords
{
    protected static string $resource = LessonResource::class;

    protected static string $relationship = 'events';

    protected static ?string $navigationIcon = '';

    public static function getNavigationLabel(): string
    {
        return 'Manage Events';
    }

    public static function canAccess(array $parameters = []): bool
    {
        return $parameters['record']['has_appointment'];
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Grid::make()->schema([
                    SpatieMediaLibraryFileUpload::make('image')
                        ->multiple()
                        ->disk('s3_public')
                        ->collection('images')
                        ->columnSpanFull()
                        ->required(),
                    TextInput::make('name')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                return;
                            }

                            $set('slug', Str::slug($state));
                        }),
                    TextInput::make('slug')
                        ->required()
                        ->unique(ignoreRecord: true)
                        ->readOnly()
                        ->maxLength(255),
                    RichEditor::make('description')
                        ->fileAttachmentsDisk('s3_public')
                        ->fileAttachmentsDirectory('attachments')
                        ->columnSpanFull(),
                    Textarea::make('url')
                        ->columnSpanFull(),
                    Select::make('meeting_type')
                        ->options(MeetingType::class),
                    Select::make('category_id')
                        ->searchable()
                        ->relationship(titleAttribute: 'name', name: 'category'),
                    DateTimePicker::make('start_time')
                        ->seconds(false),
                    DateTimePicker::make('end_time')
                        ->seconds(false),
                    KeyValue::make('meta')
                        ->default([
                            'title' => '',
                            'description' => ''
                        ])->columnSpanFull(),
                    Toggle::make('is_paid'),
                    Toggle::make('status'),
                ])->columns(2),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name'),
            ])
            ->filters([])
            ->headerActions([
                Tables\Actions\CreateAction::make()->slideOver(),
                Tables\Actions\AttachAction::make()
                    ->recordSelectOptionsQuery(fn(Builder $query) => $query->where('user_id', auth()->user()->id)),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DetachAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DetachBulkAction::make(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
