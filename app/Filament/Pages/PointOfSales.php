<?php

namespace App\Filament\Pages;

use App\Models\Cart;
use App\Models\Product;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Tables\Table;
use Filament\Forms\Contracts\HasForms;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ViewColumn;
use Filament\Tables\Contracts\HasTable;
use Illuminate\Database\Eloquent\Model;
use Filament\Tables\Columns\Layout\Grid;
use Filament\Tables\Columns\Layout\Stack;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;

class PointOfSales extends Page
{
    protected $carts;

    protected static ?string $navigationIcon = 'hugeicons-cashier-02';

    protected static ?string $navigationGroup = 'Transactions';

    protected static ?string $navigationLabel = 'POS';

    protected static string $view = 'filament.pages.point-of-sales';

    public static function canAccess(): bool
    {
        return auth()->user()->can(['page_PointOfSales']);
    }

    public function mount(): void
    {
        $this->carts = Cart::whereHas('model', function (Builder $query) {
            $query->where('model_type', Product::class);
        })->paginate();
    }
}
