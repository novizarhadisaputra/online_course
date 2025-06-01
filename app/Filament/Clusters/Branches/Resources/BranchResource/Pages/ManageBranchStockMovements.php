<?php

namespace App\Filament\Clusters\Branches\Resources\BranchResource\Pages;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\StockMovement;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\ManageRelatedRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use App\Filament\Clusters\Branches\Resources\BranchResource;
use Guava\FilamentNestedResources\Concerns\NestedRelationManager;

class ManageBranchStockMovements extends ManageRelatedRecords
{
    use NestedPage; // Since this is a standalone page, we also need this trait
    use NestedRelationManager;

    protected static string $resource = BranchResource::class;

    protected static string $relationship = 'stock_movements';

    protected static ?string $navigationIcon = 'carbon-asset-movement';

    protected static ?string $navigationLabel = 'Stock Movements';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('product.name')
                    ->required()
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('product.name')
            ->columns([
                TextColumn::make('branch.name'),
                TextColumn::make('product.name')
                    ->description(fn(StockMovement $record): string => $record->notes)
                    ->searchable(),
                IconColumn::make('type')
                    ->icon(fn(string $state): string => match ($state) {
                        'in' => 'heroicon-s-chevron-double-up',
                        'out' => 'heroicon-s-chevron-double-down',
                    })
                    ->color(fn(string $state): string => match ($state) {
                        'in' => 'success',
                        'out' => 'warning',
                    }),
                TextColumn::make('qty'),
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
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
                // Tables\Actions\AssociateAction::make(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                // Tables\Actions\EditAction::make(),
                // Tables\Actions\DissociateAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    // Tables\Actions\DissociateBulkAction::make(),
                    // Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
