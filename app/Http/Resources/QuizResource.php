<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Resources\AnswerResource;
use App\Http\Resources\OptionResource;
use Illuminate\Http\Resources\Json\JsonResource;

class QuizResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $answer = $this->answer()->where('user_id', $request->user()->id)->first();

        return [
            "id" => $this->id,
            "text" => $this->text,
            "options" => OptionResource::collection($this->options),
            'answer' => new AnswerResource($answer),
            'attachment' => $this->hasMedia('attachments') ? $this->getMedia('attachments')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
        ];
    }
}
