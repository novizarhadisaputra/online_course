<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\OptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
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
            "text" => $this->text,
            "options" => OptionResource::collection($this->options),
        ];
    }
}
