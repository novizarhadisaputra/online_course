<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'attachment' => $this->hasMedia('attachments') ? $this->getMedia('attachments')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
        ];
    }
}
