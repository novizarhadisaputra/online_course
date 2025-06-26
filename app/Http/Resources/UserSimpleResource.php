<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSimpleResource extends JsonResource
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
            'image' => $this->hasMedia('avatars') ? $this->getMedia('avatars')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'specialist' => $this->specialist,
        ];
    }
}
