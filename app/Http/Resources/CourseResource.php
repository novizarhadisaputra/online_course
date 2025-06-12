<?php

namespace App\Http\Resources;

use App\Models\Course;
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
        $id = $this->id;
        $is_like = !$request->user() ? false : $this->likes()->where('user_id', $request->user()->id)->exists();
        $is_buy = !$request->user() ? false : $this->transactions()
            ->where('user_id', $request->user()->id)
            ->where('status', TransactionStatus::SUCCESS)
            ->exists();
        $progress = !$request->user() ? null : $request->user()->progressCourses()->where('courses.id', $id)->orderBy('created_at', 'desc')->first();
        $enrollment = !$request->user() ? [] : $this->enrollments()->where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->first();

        return [
            'id' => $id,
            'image' => $this->hasMedia('images') ? $this->getMedia('images')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'thumbnail' => $this->hasMedia('thumbnails') ? $this->getMedia('thumbnails')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'preview' => $this->hasMedia('previews') ? $this->getMedia('previews')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'name' => $this->name,
            'slug' => $this->slug,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'requirement' => $this->requirement,
            'duration' => $this->duration,
            'duration_units' => $this->duration_units,
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
            'students' => $this->transactions()->select(['id'])->count(),
            'sections' => $this->sections()->select(['id'])->where('sections.status', true)->count(),
            'lessons' => $this->lessons()->select(['id'])->where('lessons.status', true)->count(),
            'total_quiz' => $this->lessons()->select(['id'])->where('lessons.status', true)->where('is_quiz', true)->count(),
            'price' => $this->is_paid ? new PriceResource($this->price) : null,
            'progress' => $progress,
            'competences' => CompetenceResource::collection($this->competences),
            'learning_methods' => LearningMethodResource::collection($this->learningMethods),
            'rating' => (object) [
                'avg' => $this->reviews()->select(['id', 'rating'])->active()->avg('rating') ?? 0,
                'count' => $this->reviews()->select(['id'])->active()->count()
            ],
            'certificate' => $enrollment ? $enrollment->certificate : null,
        ];
    }
}
