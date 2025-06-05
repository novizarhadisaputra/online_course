<?php

namespace App\Filament\Resources\PaymentGatewayResource\Pages;

use App\Filament\Resources\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListPaymentGateways extends ListRecords
{
    use NestedPage;

    protected static string $resource = PaymentGatewayResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
