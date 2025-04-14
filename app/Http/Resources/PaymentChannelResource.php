<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentChannelResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->hasMedia('images') ? $this->getMedia('images')->first()->getFullUrl() : null,
            'methods' => PaymentMethodResource::collection($this->payment_methods)
        ];
    }
}
