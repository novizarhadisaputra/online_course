<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
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
            'item' => $this->model,
            'type' => $this->model_type,
            'qty' => $this->qty,
            'price' => $this->price->value,
            'units' => $this->price->units,
            'total_price' => $this->qty * $this->price->value,
            'created_at' => $this->created_at,
        ];
    }
}
