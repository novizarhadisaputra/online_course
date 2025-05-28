<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class BundleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $courses = $this->courses()->select(['id', 'name', 'description'])->get()->toArray();
        $products = $this->products()->select(['id', 'name', 'description'])->get()->toArray();
        $items = array_merge($courses, $products);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->hasMedia('images') ? $this->getMedia('images')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'items' => $items,
        ];
    }
}
