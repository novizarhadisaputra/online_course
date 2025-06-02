<?php

namespace App\Filament\Clusters\Products\Resources;

use Filament\Tables;
use App\Models\Product;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Filament\Clusters\Products;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Clusters\Products\Resources\ProductResource\Pages;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-archive-box';

    protected static ?string $cluster = Products::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getSelectCategoryField(): Select
    {
        return Select::make('product_category_id')
            ->searchable()
            ->relationship(titleAttribute: 'name', name: 'product_category')
            ->createOptionForm([
                SpatieMediaLibraryFileUpload::make('image')
                    ->collection('images')
                    ->visibility('private')
                    ->disk('s3')
                    ->image()
                    ->previewable()
                    ->required(),
                TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255),
                RichEditor::make('description')
                    ->fileAttachmentsDisk('s3')
                    ->fileAttachmentsDirectory('attachments')
                    ->fileAttachmentsVisibility('private'),
                Toggle::make('status')
                    ->required(),
            ])
            ->required();
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
                    Grid::make()->schema([
                        TextInput::make('name')
                            ->required()
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                if (($get('slug') ?? '') !== Str::slug($old)) {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            })
                            ->unique(ignoreRecord: true)
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->readOnly()
                            ->required(),
                        TextInput::make('sku')
                            ->required(),
                    ]),
                    TextInput::make('short_description')
                        ->maxLength(255)
                        ->required(),
                    RichEditor::make('description')
                        ->fileAttachmentsDisk('s3')
                        ->fileAttachmentsDirectory('attachments')
                        ->fileAttachmentsVisibility('private')
                        ->required(),
                    self::getSelectCategoryField(),
                    KeyValue::make('meta')
                        ->default([
                            'title' => '',
                            'description' => ''
                        ])
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
                SpatieMediaLibraryImageColumn::make('image')
                    ->collection('images')
                    ->visibility('private')
                    ->disk('s3'),
                TextColumn::make('name')
                    ->searchable(),
                TextColumn::make('product_category.name')
                    ->searchable(),
                TextColumn::make('user.name')
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
