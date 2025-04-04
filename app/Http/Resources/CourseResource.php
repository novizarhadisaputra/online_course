<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $is_like = !$request->user() ? false : $this->likes()->where('user_id', $request->user()->id)->exists();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'requirement' => $this->requirement,
            'duration' => $this->duration,
            'level' => $this->level,
            'meta' => $this->meta,
            'language' => $this->language,
            'status' => $this->status,
            'is_get_certificate' => $this->is_get_certificate,
            'author' => new InstructorResource($this->user),
            'is_like' => $is_like,
            'category' => new CategoryResource($this->category),
            'tags' => TagResource::collection($this->tags),
            'lessons' => $this->lessons->count(),
            'transactions' => $this->transactions->count(),
            'prices' => PriceResource::collection($this->prices),
        ];
    }
}
