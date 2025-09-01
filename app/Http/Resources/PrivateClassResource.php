<?php

namespace App\Http\Resources;

use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class PrivateClassResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $courses = $this->courses()
            ->select(['id', 'name', 'slug', 'description'])
            ->get()
            ->transform(fn(Course $item, int $index) => (object) [
                'id' => $item->id,
                'name' => $item->name,
                'slug' => $item->slug,
                'image' => $item->hasMedia('images') ? $item->getMedia('images')->first()->getFullUrl() : null,
                'description' => $item->description
            ])
            ->toArray();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->hasMedia('images') ? $this->getMedia('images')->first()->getFullUrl() : null,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'duration' => $this->duration,
            'duration_units' => $this->duration_units,
            'max_participants' => $this->max_participants,
            'start_date' => $this->start_date ? Carbon::parse($this->start_date)->format('Y-m-d H:i:s') : null,
            'end_date' => $this->end_date ? Carbon::parse($this->end_date)->format('Y-m-d H:i:s') : null,
            'status' => $this->status,
            'is_paid' => $this->is_paid,
            'courses' => $courses,
            'metadata' => $this->metadata ? $this->metadata->data : null,
            'price' => $this->is_paid ? $this->price : null,
            'created_at' => $this->created_at ? Carbon::parse($this->created_at)->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? Carbon::parse($this->updated_at)->format('Y-m-d H:i:s') : null,
        ];
    }
}