<?php

namespace App\Livewire\PointOfSales;

use Filament\Tables;
use App\Models\Product;
use Livewire\Component;
use Filament\Tables\Table;
use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Illuminate\Contracts\View\View;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\Facades\Log;

class ListProducts extends Component implements HasForms, HasTable, HasActions
{
    use InteractsWithForms;
    use InteractsWithActions;
    use InteractsWithTable;

    public Product $product;

    public function delete()
    {
        return Action::make('delete')
            ->requiresConfirmation()
            ->action(fn() => Log::info('delete function'. $this->product->name));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Product::query())
            ->columns([
                Grid::make()
                    ->columns(1)
                    ->schema([
                        SpatieMediaLibraryImageColumn::make('image')
                            ->collection('images')
                            ->extraImgAttributes(['class' => 'w-full rounded'])
                            ->height('auto'),
                        Grid::make()
                            ->columns(2)
                            ->schema([
                                Stack::make([])->schema([
                                    TextColumn::make('name')
                                        ->alignStart()
                                        ->weight(FontWeight::Bold)
                                        ->searchable(),
                                    TextColumn::make('category.name')
                                        ->alignStart()
                                        ->searchable()
                                ]),
                                ViewColumn::make('cart')->view('filament.tables.columns.button')
                            ])
                    ])
            ])
            ->filters([
                //
            ])
            ->actions([
                //
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //
                ]),
            ])->contentGrid([
                'md' => 3,
                'xl' => 4,
            ]);
    }

    public function render(): View
    {
        return view('livewire.point-of-sales.list-products');
    }
}
