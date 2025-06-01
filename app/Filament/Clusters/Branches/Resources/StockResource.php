<?php

namespace App\Filament\Clusters\Branches\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Stock;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Filament\Clusters\Branches;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\KeyValue;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\Unique;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Illuminate\Contracts\Support\Htmlable;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Filament\Clusters\Branches\Resources\StockResource\Pages;

class StockResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Stock::class;

    protected static ?string $navigationIcon = 'heroicon-o-presentation-chart-line';

    protected static ?string $cluster = Branches::class;

    public static function getSelectBranchField(): Select
    {
        return Select::make('branch_id')
            ->relationship(titleAttribute: 'name', name: 'branch')
            ->createOptionForm([
                Grid::make()->schema([
                    TextInput::make('name')
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                            if (($get('slug') ?? '') !== Str::slug($old)) {
                                return;
                            }
                            $set('code', Str::upper(Str::random(10)));
                            $set('slug', Str::slug($state));
                        })
                        ->unique(ignoreRecord: true)
                        ->maxLength(255),
                    TextInput::make('slug')
                        ->readOnly()
                        ->required(),
                    Hidden::make('code')

                ]),
                RichEditor::make('description'),
                Toggle::make('status')
                    ->required(),
            ])
            ->searchable()
            ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                return $rule
                    ->where('branch_id', $get('branch_id'))
                    ->where('product_id', $get('product_id'));
            })
            ->required();
    }

    public static function getSelectProductField(): Select
    {
        return Select::make('product_id')
            ->relationship(titleAttribute: 'name', name: 'product')
            ->searchable()
            ->createOptionForm([
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
                ]),
                TextInput::make('short_description')
                    ->maxLength(255)
                    ->required(),
                RichEditor::make('description')
                    ->required(),
                Select::make('product_category_id')
                    ->searchable()
                    ->relationship(titleAttribute: 'name', name: 'product_category')
                    ->required(),
                KeyValue::make('meta')
                    ->default([
                        'title' => '',
                        'description' => ''
                    ])
                    ->required(),
                Toggle::make('status')
                    ->required(),
            ])
            ->unique(modifyRuleUsing: function (Unique $rule, callable $get) {
                return $rule
                    ->where('branch_id', $get('branch_id'))
                    ->where('product_id', $get('product_id'));
            })
            ->required();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
                    self::getSelectBranchField(),
                    self::getSelectProductField(),
                    TextInput::make('qty')
                        ->required()
                        ->numeric()
                        ->default(0),
                ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('branch.name')->searchable(),
                TextColumn::make('product.name')->searchable(),
                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
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
            'index' => Pages\ListStocks::route('/'),
            'create' => Pages\CreateStock::route('/create'),
            'view' => Pages\ViewStock::route('/{record}'),
            'edit' => Pages\EditStock::route('/{record}/edit'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'stocks', // Relationship name
            'model', // Inverse relationship name
        );
    }
}
