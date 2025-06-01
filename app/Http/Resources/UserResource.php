<?php

namespace App\Http\Resources;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
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
            'image' => $this->hasMedia('avatars') ? $this->getMedia('avatars')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            "name" => $this->name,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "description" => $this->description,
            "email" => $this->email ? Str::mask($this->email, '*', 3) : null,
            "phone" => $this->phone ? Str::mask($this->phone, '*', 3) : null,
            "gender" => $this->gender,
            'followers_count' => $this->followers()->select(['id'])->count(),
            'following_count' => $this->following()->select(['id'])->count(),
        ];
    }
}
