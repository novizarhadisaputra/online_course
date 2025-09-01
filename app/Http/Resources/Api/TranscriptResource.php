<?php

namespace App\Http\Resources\Api;

use App\Models\Course;
use App\Models\Bundle;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TranscriptResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $courseData = $this->getCourseData();
        
        return [
            'id' => $this->id,
            'student' => [
                'id' => $this->user->id,
                'name' => $this->user->name,
                'email' => $this->user->email,
            ],
            'course_info' => $courseData,
            'score_details' => [
                'value' => $this->value,
                'grade_letter' => $this->getGradeLetter($this->value),
                'grade_point' => $this->getGradePoint($this->value),
                'is_graduated' => $this->is_graduated,
                'semester' => $this->batches,
            ],
            'academic_info' => [
                'academic_year' => $this->created_at->year,
                'completion_date' => $this->updated_at->format('Y-m-d'),
                'enrollment_date' => $this->created_at->format('Y-m-d'),
            ],
            'metadata' => [
                'model_type' => $this->model_type,
                'model_id' => $this->model_id,
                'created_at' => $this->created_at->toISOString(),
                'updated_at' => $this->updated_at->toISOString(),
            ]
        ];
    }

    /**
     * Get course data based on model type
     *
     * @return array
     */
    private function getCourseData(): array
    {
        if ($this->model_type === Course::class) {
            return $this->getSingleCourseData($this->model);
        } elseif ($this->model_type === Bundle::class) {
            return $this->getBundleData($this->model);
        }

        return [
            'type' => 'unknown',
            'name' => 'Unknown Course Type',
            'code' => 'N/A',
            'credits' => 0,
        ];
    }

    /**
     * Get single course data
     *
     * @param Course $course
     * @return array
     */
    private function getSingleCourseData(Course $course): array
    {
        $credits = $this->getCourseCredits($course);
        $courseCode = $this->getCourseCode($course);

        return [
            'type' => 'course',
            'id' => $course->id,
            'name' => $course->name,
            'code' => $courseCode,
            'credits' => $credits,
            'description' => $course->description,
            'level' => $course->level,
            'language' => $course->language,
            'duration' => $course->duration,
            'category' => [
                'id' => $course->category?->id,
                'name' => $course->category?->name,
            ],
            'status' => $course->status,
            'is_paid' => $course->is_paid,
        ];
    }

    /**
     * Get bundle data with courses
     *
     * @param Bundle $bundle
     * @return array
     */
    private function getBundleData(Bundle $bundle): array
    {
        $totalCredits = 0;
        $courses = [];

        if ($bundle->courses) {
            foreach ($bundle->courses as $course) {
                $credits = $this->getCourseCredits($course);
                $totalCredits += $credits;
                
                $courses[] = [
                    'id' => $course->id,
                    'name' => $course->name,
                    'code' => $this->getCourseCode($course),
                    'credits' => $credits,
                    'level' => $course->level,
                    'category' => [
                        'id' => $course->category?->id,
                        'name' => $course->category?->name,
                    ],
                ];
            }
        }

        return [
            'type' => 'bundle',
            'id' => $bundle->id,
            'name' => $bundle->name,
            'code' => $bundle->slug,
            'credits' => $totalCredits,
            'description' => $bundle->description,
            'duration' => $bundle->duration,
            'status' => $bundle->status,
            'is_paid' => $bundle->is_paid,
            'courses' => $courses,
            'total_courses' => count($courses),
        ];
    }

    /**
     * Get course credits from metadata or default
     *
     * @param Course $course
     * @return int
     */
    private function getCourseCredits(Course $course): int
    {
        // Try to get credits from metadata
        if ($course->metadata && isset($course->metadata->data['credits'])) {
            return (int) $course->metadata->data['credits'];
        }

        // Try to get from course meta field
        if ($course->meta && isset($course->meta['credits'])) {
            return (int) $course->meta['credits'];
        }

        // Default to 3 credits
        return 3;
    }

    /**
     * Get course code from metadata or generate from name
     *
     * @param Course $course
     * @return string
     */
    private function getCourseCode(Course $course): string
    {
        // Try to get code from metadata
        if ($course->metadata && isset($course->metadata->data['code'])) {
            return $course->metadata->data['code'];
        }

        // Try to get from course meta field
        if ($course->meta && isset($course->meta['code'])) {
            return $course->meta['code'];
        }

        // Generate code from course name (first 3 letters + random number)
        $nameCode = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $course->name), 0, 3));
        return $nameCode . sprintf('%03d', $course->id % 1000);
    }

    /**
     * Convert numeric score to letter grade
     *
     * @param float $score
     * @return string
     */
    private function getGradeLetter(float $score): string
    {
        if ($score >= 85) return 'A';
        if ($score >= 80) return 'A-';
        if ($score >= 75) return 'B+';
        if ($score >= 70) return 'B';
        if ($score >= 65) return 'B-';
        if ($score >= 60) return 'C+';
        if ($score >= 55) return 'C';
        if ($score >= 50) return 'C-';
        if ($score >= 45) return 'D+';
        if ($score >= 40) return 'D';
        return 'F';
    }

    /**
     * Convert numeric score to grade point
     *
     * @param float $score
     * @return float
     */
    private function getGradePoint(float $score): float
    {
        if ($score >= 85) return 4.0;
        if ($score >= 80) return 3.7;
        if ($score >= 75) return 3.3;
        if ($score >= 70) return 3.0;
        if ($score >= 65) return 2.7;
        if ($score >= 60) return 2.3;
        if ($score >= 55) return 2.0;
        if ($score >= 50) return 1.7;
        if ($score >= 45) return 1.3;
        if ($score >= 40) return 1.0;
        return 0.0;
    }
}