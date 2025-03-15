<?php

namespace App\Filament\Resources\PaymentGatewayResource\Pages;

use App\Filament\Resources\PaymentGatewayResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreatePaymentGateway extends CreateRecord
{
    use NestedPage;

    protected static string $resource = PaymentGatewayResource::class;
}
