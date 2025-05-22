<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'image' => $this->hasMedia('avatars') ? $this->getMedia('avatars')->first()->getFullUrl() : null,
            "name" => $this->name,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "description" => $this->description,
            "email" => $this->email,
            "phone" => $this->phone,
            "gender" => $this->gender,
            'followers_count' => $this->followers()->select(['id'])->count(),
            'following_count' => $this->following()->select(['id'])->count(),
        ];
    }
}
