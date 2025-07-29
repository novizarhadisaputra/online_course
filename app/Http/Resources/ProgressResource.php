<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProgressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = 'not started';
        if ($this->data != null && isset($this->data['percentage'])) {
            if ($this->data['percentage'] > 0) {
                $status = 'ongoing';
                if ($this->data['percentage'] == 100) {
                    $status = 'completed';
                }
            }
        }

        return [
            'id' => $this->id,
            'type' => $this->model_type,
            'slug' => $this->model->slug,
            'name' => $this->model->name,
            'data' => $this->data,
            'status' =>  $status,
        ];
    }
}
