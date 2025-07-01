<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class AnswerResource extends JsonResource
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
            'text' => $this->text,
            'data' => $this->data,
            'type' => $this->model_type,
            'option' => new OptionResource($this->option),
        ];
    }
}
