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

    // We can usually guess the nested resource, but if your app has multiple resources for this
    // model, you will need to explicitly define it
    public static string $nestedResource = PaymentMethodResource::class;
}
