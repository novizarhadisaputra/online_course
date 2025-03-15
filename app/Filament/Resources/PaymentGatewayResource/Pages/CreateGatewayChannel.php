<?php

namespace App\Filament\Resources\PaymentGatewayResource\Pages;

use App\Filament\Resources\PaymentGatewayResource;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateGatewayChannel extends CreateRelatedRecord
{
    use NestedPage;

    protected static string $resource = PaymentGatewayResource::class;

    protected static string $relationship = 'channels';
}
