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
        $data = null;
        if ($this->configs && $this->configs['account_name']) {
            $data = (object) [
                'account_name' => $this->configs['account_name'],
                'account_number' => $this->configs['account_number'],
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->hasMedia('images') ? $this->getMedia('images')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'data' => $data,
        ];
    }
}
