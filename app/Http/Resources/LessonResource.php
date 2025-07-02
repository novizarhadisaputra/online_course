<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\TransactionStatus;
use App\Models\Answer;
use Illuminate\Database\Eloquent\Builder;
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
        $user = $request->user();
        $id = $this->id;
        $is_buy = !$user ? false : $this->section->course->transactions()
            ->where('user_id', $user->id)
            ->where('status', TransactionStatus::SUCCESS)
            ->exists();
        $is_like = !$user ? false : $this->likes()->where('user_id', $user->id)->exists();
        $score = !$user ? null : $this->score()->where('user_id', $user->id)->first();
        $quiz_count = $this->quizzes()->select(['id'])->count();
        $time_left = 0;
        if ($quiz_count && $user) {
            $answer = Answer::where('user_id', $user->id)->whereHas('model', function (Builder $quiz) use ($id) {
                $quiz->whereHas('model', function (Builder $lesson) use ($id) {
                    $lesson->where('id', $id);
                });
            });
            if ($answer && $answer->data && $answer->data['time_left']) {
                $time_left = $answer->data['time_left'];
            }
        }

        return [
            "id" => $id,
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
            'title_assignment' => $this->title_assignment,
            'description_assignment' => $this->description_assignment,
            'due_date' => $this->due_date,
            'time_left' => $time_left,
        ];
    }
}
