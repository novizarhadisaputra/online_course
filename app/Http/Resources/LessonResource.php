<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\TransactionStatus;
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
        $is_buy = !$request->user() ? false : $this->section()->course()->transactions()
            ->where('user_id', $request->user()->id)
            ->where('status', TransactionStatus::SUCCESS)
            ->exists();
        $is_like = !$request->user() ? false : $this->likes()->where('user_id', $request->user()->id)->exists();
        $score = !$request->user() ? null : $this->score()->where('user_id', $request->user()->id)->first();
        $quiz_count = $this->quizzes()->select(['id'])->count();

        return [
            "id" => $this->id,
            "name" => $this->name,
            'attachment' => ($is_buy && $this->hasMedia('attachments')) ? $this->getMedia('attachments')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            "short_description" => $this->short_description,
            "description" => $this->description,
            'duration' => $this->duration,
            'duration_units' => $this->duration_units,
            "is_quiz" => $this->is_quiz,
            "is_paid" => $this->is_paid,
            'is_like' => $is_like,
            "quiz_count" => $quiz_count,
            "score" => $score,
        ];
    }
}
