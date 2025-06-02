<?php

namespace App\Filament\Clusters\Branches\Resources;

use Filament\Tables;
use App\Models\Branch;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Filament\Resources\Resource;
use App\Filament\Clusters\Branches;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Section;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\RichEditor;
use Guava\FilamentNestedResources\Ancestor;
use Guava\FilamentNestedResources\Concerns\NestedResource;
use App\Filament\Clusters\Branches\Resources\BranchResource\Pages;

class BranchResource extends Resource
{
    use NestedResource;

    protected static ?string $model = Branch::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $cluster = Branches::class;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()->schema([
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
                    RichEditor::make('description'),
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
                TextColumn::make('slug')
                    ->searchable(),
                TextColumn::make('code')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Code copied')
                    ->copyMessageDuration(1500),
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
            'index' => Pages\ListBranches::route('/'),
            'create' => Pages\CreateBranch::route('/create'),
            'view' => Pages\ViewBranch::route('/{record}'),
            'edit' => Pages\EditBranch::route('/{record}/edit'),
            'stocks' => Pages\ManageBranchStocks::route('/{record}/stocks'),
            'users' => Pages\ManageBranchUsers::route('/{record}/users'),
            'stocks.create' => Pages\CreateBranchStock::route('/{record}/stocks/create'),
            'stock_movements' => Pages\ManageBranchStockMovements::route('/{record}/stock-movements'),
            'stock_movements.create' => Pages\CreateBranchMovements::route('/{record}/stock-movements/create'),
        ];
    }

    public static function getRecordSubNavigation(Page $page): array
    {
        return $page->generateNavigationItems([
            Pages\ViewBranch::class,
            Pages\EditBranch::class,
            Pages\ManageBranchUsers::class,
            Pages\ManageBranchStocks::class,
            Pages\ManageBranchStockMovements::class,
        ]);
    }

    public static function getAncestor(): ?Ancestor
    {
        return null;
    }
}
