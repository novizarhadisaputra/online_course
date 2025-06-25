<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
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
            'code' => $this->code,
            'payment_method' => new PaymentMethodResource($this->payment_method),
            'payment_link' => $this->payment_link,
            'service_fee' => $this->service_fee,
            'tax_fee' => $this->tax_fee,
            'tax_percentage' => $this->tax_percentage,
            'total_qty' => $this->total_qty,
            'total_price' => $this->total_price,
            'total_paid' => $this->total_price + $this->service_fee + $this->tax_fee,
            'status' => $this->status,
            'category' => $this->category,
            'address' => new AddressResource($this->address),
            'data' => $this->data,
            'details' => TransactionDetailResource::collection($this->details),
            'created_at' => $this->created_at,
        ];
    }
}
