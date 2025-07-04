<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "short_description" => $this->short_description,
            "description" => $this->description,
            "status" => $this->status,
            "lessons" => $this->lessons()->select(['id', 'name', 'duration', 'duration_units'])->get()
        ];
    }
}
