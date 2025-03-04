<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'commentable' => $this->commentable,
            'description' => $this->description,
            'status' => $this->status,
            'user' => new UserResource($this->user),
            'comments_count' => $this->comments()->count(),
        ];
    }
}
