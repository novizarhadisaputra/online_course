<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentMethodResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $data = (object) [
            'code' => $this->configs['code'] ?? null,
            'service_fee' => $this->configs['service_fee'] ?? null,
            'service_fee_type' => $this->configs['service_fee_type'] ?? null,
            'tax_fee' => $this->configs['tax_fee'] ?? null,
            'tax_fee_type' => $this->configs['tax_fee_type'] ?? null,
        ];
        if ($this->configs && isset($this->configs['account_name'])) {
            $data->account_name = $this->configs['account_name'];
            $data->account_number = $this->configs['account_number'];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->hasMedia('images') ? $this->getMedia('images')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'data' => $data,
        ];
    }
}
