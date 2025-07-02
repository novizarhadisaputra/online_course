<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\TransactionStatus;
use App\Models\Answer;
use App\Models\Quiz;
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
            $ids = Quiz::with(['model', 'answers'])->whereHas('model', function (Builder $model) use ($id) {
                $model->where('id', $id);
            })->select(['id'])->groupBy('id')->orderBy('created_at', 'desc')->select(['id'])->pluck('id')->toArray();
            $answer = Answer::whereIn('model_id', $ids)
                ->orderBy('created_at', 'desc')
                ->first();
            if ($answer && isset($answer->data) && isset($answer->data['time_left'])) {
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
