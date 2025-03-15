<?php

namespace App\Filament\Resources\PaymentChannelResource\Pages;

use App\Filament\Resources\PaymentChannelResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Guava\FilamentNestedResources\Concerns\NestedPage;

class ListPaymentChannels extends ListRecords
{
    use NestedPage;

    protected static string $resource = PaymentChannelResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
