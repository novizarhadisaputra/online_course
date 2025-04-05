<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InstructorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $is_following = !$request->user() ? false : $this->following()->where('user_id', $request->user()->id)->exists();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'image' => $this->hasMedia('avatars') ? $this->getMedia('avatars')->first()->getFullUrl() : null,
            'is_following' => $is_following,
        ];
    }
}
