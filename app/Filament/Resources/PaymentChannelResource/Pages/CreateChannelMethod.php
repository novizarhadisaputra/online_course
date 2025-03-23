<?php

namespace App\Filament\Resources\PaymentChannelResource\Pages;

use App\Filament\Resources\PaymentChannelResource;
use App\Filament\Resources\PaymentMethodResource;
use Guava\FilamentNestedResources\Concerns\NestedPage;
use Guava\FilamentNestedResources\Pages\CreateRelatedRecord;

class CreateChannelMethod extends CreateRelatedRecord
{
    use NestedPage;

    protected static string $resource = PaymentChannelResource::class;

    protected static string $relationship = 'methods';
}
