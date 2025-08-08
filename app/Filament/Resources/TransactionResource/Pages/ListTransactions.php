<?php

namespace App\Filament\Resources\TransactionResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\TransactionResource;
use App\Models\Transaction;
use Illuminate\Contracts\Database\Eloquent\Builder;

class ListTransactions extends ListRecords
{
    protected static string $resource = TransactionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->visible(auth()->user()->hasRole(['Developer'])),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make()
                ->badge(Transaction::query()->count()),
            'success' => Tab::make()
                ->badge(Transaction::query()->where('status', 'success')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'success')),
            'waiting payment' => Tab::make()
                ->badge(Transaction::query()->where('status', 'waiting payment')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'waiting payment')),
            'pending' => Tab::make()
                ->badge(Transaction::query()->where('status', 'pending')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'pending')),
            'cancel' => Tab::make()
                ->badge(Transaction::query()->where('status', 'cancel')->count())
                ->modifyQueryUsing(fn(Builder $query) => $query->where('status', 'cancel')),
        ];
    }
}
