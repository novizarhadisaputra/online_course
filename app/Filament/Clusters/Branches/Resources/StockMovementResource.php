<?php

namespace App\Filament\Clusters\Branches\Resources;

use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StockMovement;
use Filament\Resources\Resource;
use App\Filament\Clusters\Branches;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Clusters\Branches\Resources\StockMovementResource\Pages;

class StockMovementResource extends Resource
{
    use NestedResource;

    protected static ?string $model = StockMovement::class;

    protected static ?string $navigationIcon = 'carbon-asset-movement';

    protected static ?string $navigationLabel = 'Stock Movements';

    protected static ?string $cluster = Branches::class;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('model_type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('model_id')
                    ->required(),
                TextInput::make('type')
                    ->required()
                    ->maxLength(255),
                TextInput::make('qty')
                    ->required()
                    ->numeric()
                    ->default(0),
                Textarea::make('notes')
                    ->columnSpanFull(),
                TextInput::make('branch_id')
                    ->required(),
                TextInput::make('product_id')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('model.name')
                    ->searchable(),
                TextColumn::make('type')
                    ->searchable(),
                TextColumn::make('qty')
                    ->numeric()
                    ->sortable(),
                TextColumn::make('branch.name'),
                TextColumn::make('product.name'),
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
            'index' => Pages\ListStockMovements::route('/'),
            'create' => Pages\CreateStockMovement::route('/create'),
            'view' => Pages\ViewStockMovement::route('/{record}'),
            'edit' => Pages\EditStockMovement::route('/{record}/edit'),
        ];
    }

    public static function getAncestor(): ?Ancestor
    {
        // Configure the ancestor (parent) relationship here
        return Ancestor::make(
            'stock_movements', // Relationship name
            'model', // Inverse relationship name
        );
    }
}
