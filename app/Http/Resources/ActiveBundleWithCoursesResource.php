<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActiveBundleWithCoursesResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'image' => $this->image,
            'short_description' => $this->short_description,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Price information
            'price' => [
                'amount' => $this->price?->amount ?? 0,
                'discount_amount' => $this->price?->discount_amount ?? 0,
                'discount_percentage' => $this->price?->discount_percentage ?? 0,
                'final_price' => ($this->price?->amount ?? 0) - ($this->price?->discount_amount ?? 0),
                'currency' => $this->price?->currency ?? 'IDR',
                'is_free' => ($this->price?->amount ?? 0) == 0
            ],
            
            // Bundle metadata
            'metadata' => [
                'total_duration' => $this->calculateTotalDuration(),
                'total_courses' => $this->courses->count(),
                'total_products' => $this->products->count(),
                'difficulty_level' => $this->getDifficultyLevel(),
                'languages' => $this->getLanguages(),
                'requirements' => $this->getRequirements(),
                'benefits' => $this->metadata?->benefits ?? [],
                'features' => $this->metadata?->features ?? []
            ],
            
            // Courses information
            'courses' => $this->courses->map(function ($course) {
                return [
                    'id' => $course->id,
                    'name' => $course->name,
                    'slug' => $course->slug,
                    'description' => $course->description,
                    'short_description' => $course->short_description,
                    'duration' => $course->duration,
                    'duration_units' => $course->duration_units,
                    'level' => $course->level,
                    'language' => $course->language,
                    'is_paid' => $course->is_paid,
                    'instructor' => [
                        'id' => $course->user?->id,
                        'name' => $course->user?->name
                    ],
                    'price' => [
                        'amount' => $course->price?->amount ?? 0,
                        'discount_amount' => $course->price?->discount_amount ?? 0,
                        'final_price' => ($course->price?->amount ?? 0) - ($course->price?->discount_amount ?? 0),
                        'currency' => $course->price?->currency ?? 'IDR'
                    ],
                    'metadata' => [
                        'requirements' => $course->metadata?->requirements ?? [],
                        'learning_outcomes' => $course->metadata?->learning_outcomes ?? [],
                        'target_audience' => $course->metadata?->target_audience ?? []
                    ]
                ];
            }),
            
            // Products information (if any)
            'products' => $this->products->map(function ($product) {
                return [
                    'id' => $product->id,
                    'name' => $product->name,
                    'slug' => $product->slug,
                    'description' => $product->description,
                    'price' => [
                        'amount' => $product->price?->amount ?? 0,
                        'discount_amount' => $product->price?->discount_amount ?? 0,
                        'final_price' => ($product->price?->amount ?? 0) - ($product->price?->discount_amount ?? 0),
                        'currency' => $product->price?->currency ?? 'IDR'
                    ],
                    'metadata' => [
                        'specifications' => $product->metadata?->specifications ?? [],
                        'features' => $product->metadata?->features ?? []
                    ]
                ];
            })
        ];
    }
    
    /**
     * Calculate total duration from all courses in the bundle
     */
    private function calculateTotalDuration(): array
    {
        $totalMinutes = 0;
        $totalHours = 0;
        
        foreach ($this->courses as $course) {
            if ($course->duration && $course->duration_units) {
                switch (strtolower($course->duration_units)) {
                    case 'minutes':
                    case 'minute':
                        $totalMinutes += $course->duration;
                        break;
                    case 'hours':
                    case 'hour':
                        $totalHours += $course->duration;
                        break;
                    case 'days':
                    case 'day':
                        $totalHours += $course->duration * 24;
                        break;
                }
            }
        }
        
        // Convert excess minutes to hours
        $totalHours += floor($totalMinutes / 60);
        $remainingMinutes = $totalMinutes % 60;
        
        return [
            'total_hours' => $totalHours,
            'total_minutes' => $remainingMinutes,
            'formatted' => $this->formatDuration($totalHours, $remainingMinutes)
        ];
    }
    
    /**
     * Format duration for display
     */
    private function formatDuration(int $hours, int $minutes): string
    {
        if ($hours > 0 && $minutes > 0) {
            return "{$hours} jam {$minutes} menit";
        } elseif ($hours > 0) {
            return "{$hours} jam";
        } elseif ($minutes > 0) {
            return "{$minutes} menit";
        }
        
        return '0 menit';
    }
    
    /**
     * Get difficulty level based on courses
     */
    private function getDifficultyLevel(): string
    {
        $levels = $this->courses->pluck('level')->filter()->unique();
        
        if ($levels->contains('advanced')) {
            return 'advanced';
        } elseif ($levels->contains('intermediate')) {
            return 'intermediate';
        } elseif ($levels->contains('beginner')) {
            return 'beginner';
        }
        
        return 'mixed';
    }
    
    /**
     * Get unique languages from courses
     */
    private function getLanguages(): array
    {
        return $this->courses->pluck('language')
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }
    
    /**
     * Get requirements from bundle metadata or courses
     */
    private function getRequirements(): array
    {
        // First try to get from bundle metadata
        if ($this->metadata && isset($this->metadata->requirements)) {
            return $this->metadata->requirements;
        }
        
        // Otherwise, collect from courses
        $requirements = [];
        foreach ($this->courses as $course) {
            if ($course->requirement) {
                $requirements[] = $course->requirement;
            }
            if ($course->metadata && isset($course->metadata->requirements)) {
                $requirements = array_merge($requirements, $course->metadata->requirements);
            }
        }
        
        return array_unique($requirements);
    }
}