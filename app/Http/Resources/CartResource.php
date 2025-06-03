<?php

namespace App\Http\Resources;

use App\Filament\Resources\EventResource;
use App\Models\Event;
use App\Models\Bundle;
use App\Models\Course;
use Illuminate\Http\Request;
use App\Http\Resources\BundleResource;
use App\Http\Resources\CourseResource;
use Illuminate\Http\Resources\Json\JsonResource;

class CartResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $item = null;
        switch ($this->model_type) {
            case Course::class:
                $item = new CourseResource($this->model);
                break;
            case Event::class:
                $item = new EventResource($this->model);
                break;
            case Bundle::class:
                $item = new BundleResource($this->model);
                break;
            default:
                break;
        }
        return [
            'id' => $this->id,
            'item' => $item,
            'qty' => $this->qty,
            'price' => $this->price->value,
            'units' => $this->price->units,
            'total_price' => $this->qty * $this->price->value,
            'created_at' => $this->created_at,
        ];
    }
}
