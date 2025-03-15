<?php

namespace App\Filament\Resources\PaymentChannelResource\Pages;

use App\Filament\Resources\PaymentChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class CreatePaymentChannel extends CreateRecord
{
    use NestedPage;

    protected static string $resource = PaymentChannelResource::class;
}
