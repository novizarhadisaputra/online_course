<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Enums\TransactionStatus;
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
        $is_buy = !$request->user() ? false : $this->transactions()
            ->where('user_id', $request->user()->id)
            ->where('status', TransactionStatus::SUCCESS)
            ->exists();
        $progress = !$request->user() ? null : $request->user()->progressCourses()->where('courses.id', $this->id)->first();

        return [
            'id' => $this->id,
            'image' => $this->hasMedia('images') ? $this->getMedia('images')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'preview' => $this->hasMedia('previews') ? $this->getMedia('previews')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
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
            'author' => new InstructorResource($this->user),
            'is_get_certificate' => $this->is_get_certificate,
            'is_like' => $is_like,
            'is_buy' => $is_buy,
            'category' => new CategoryResource($this->category),
            'tags' => TagResource::collection($this->tags),
            'lessons' => $this->lessons->count(),
            'students' => $this->transactions->count(),
            'price' => new PriceResource($this->price),
            'progress' => $progress,
            'competences' => $this->competences,
            'learning_methods' => $this->learningMethods,
        ];
    }
}
