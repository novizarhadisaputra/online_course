<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class CertificateResource extends JsonResource
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
            'certificate' => $this->hasMedia('certificates') ? $this->getMedia('certificates')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'certificate_number' => $this->certificate_number,
            'item_name' => $this->model->model ? $this->model->model->name : $this->model->name,
            'item_type' => $this->model->model ? $this->model->model_type : $this->model_type,
            'date' => $this->created_at,
        ];
    }
}
