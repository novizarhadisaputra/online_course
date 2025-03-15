<?php

namespace App\Filament\Resources\PaymentMethodResource\Pages;

use App\Filament\Resources\PaymentMethodResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreatePaymentMethod extends CreateRecord
{
    use NestedPage;

    protected static string $resource = PaymentMethodResource::class;
}
