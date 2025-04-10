<?php

namespace App\Filament\Resources\CouponResource\Pages;

use Filament\Actions;
use App\Models\Coupon;
use Illuminate\Support\Str;
use App\Filament\Resources\CouponResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCoupon extends CreateRecord
{
    protected static string $resource = CouponResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $code = Str::upper(Str::random(10));
        $existCode = Coupon::where('code', $code)->exists();
        while ($existCode) {
            $code = Str::upper(Str::random(10));
            $existCode = Coupon::where('code', $code)->exists();
        }

        $data['code'] = $code;

        return $data;
    }
}
