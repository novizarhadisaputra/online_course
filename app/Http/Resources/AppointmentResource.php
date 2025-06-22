<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\InstructorResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AppointmentResource extends JsonResource
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
            'name' => $this->model->name,
            'code' => $this->code,
            'date' => $this->date,
            'is_attended' => $this->is_attended,
            'check_in_at' => $this->check_in_at,
            'check_out_at' => $this->check_out_at,
            'source' => new InstructorResource($this->source),
        ];
    }
}
