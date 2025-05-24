<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LessonResource extends JsonResource
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
            "name" => $this->name,
            'attachment' => $this->hasMedia('attachments') ? $this->getMedia('attachments')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            "short_description" => $this->short_description,
            "description" => $this->description,
            "is_quiz" => $this->is_quiz,
            "is_paid" => $this->is_paid,
            "quiz_count" => $this->quizzes()->count(),
        ];
    }
}
