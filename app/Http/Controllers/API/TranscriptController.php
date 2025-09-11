<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\TranscriptRequest;
use App\Http\Resources\Api\TranscriptResource;
use App\Models\Score;
use App\Models\Bundle;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class TranscriptController extends Controller
{
    /**
     * Get student transcript data
     *
     * @param TranscriptRequest $request
     * @return JsonResponse
     */
    public function index(TranscriptRequest $request): JsonResponse
    {
        try {
            $query = Score::with([
                'model' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['category', 'metadata'],
                        Bundle::class => ['courses.category', 'courses.metadata', 'metadata']
                    ]);
                },
                'user'
            ]);

            // Filter by student ID
            if ($request->filled('student_id')) {
                $query->where('user_id', $request->student_id);
            } else {
                // If no student_id provided, use authenticated user
                $query->where('user_id', Auth::id());
            }

            // Filter by semester (assuming batches represents semester)
            if ($request->filled('semester')) {
                $query->where('batches', $request->semester);
            }

            // Filter by academic year (using created_at year)
            if ($request->filled('academic_year')) {
                $query->whereYear('created_at', $request->academic_year);
            }

            // Filter by graduation status
            if ($request->filled('is_graduated')) {
                $query->where('is_graduated', $request->boolean('is_graduated'));
            }

            // Order by latest first
            $query->orderBy('created_at', 'desc');

            $scores = $query->paginate($request->get('per_page', 15));

            return response()->json([
                'success' => true,
                'message' => 'Transcript data retrieved successfully',
                'data' => TranscriptResource::collection($scores),
                'meta' => [
                    'current_page' => $scores->currentPage(),
                    'last_page' => $scores->lastPage(),
                    'per_page' => $scores->perPage(),
                    'total' => $scores->total(),
                    'from' => $scores->firstItem(),
                    'to' => $scores->lastItem(),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transcript data',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get detailed transcript for specific student
     *
     * @param string $studentId
     * @param Request $request
     * @return JsonResponse
     */
    public function show(string $studentId, Request $request): JsonResponse
    {
        try {
            // Verify student exists
            $student = User::findOrFail($studentId);

            $query = Score::with([
                'model' => function ($morphTo) {
                    $morphTo->morphWith([
                        Course::class => ['category', 'metadata', 'sections.lessons'],
                        Bundle::class => ['courses.category', 'courses.metadata', 'courses.sections.lessons', 'metadata']
                    ]);
                },
                'user'
            ])->where('user_id', $studentId);

            // Apply filters if provided
            if ($request->filled('semester')) {
                $query->where('batches', $request->semester);
            }

            if ($request->filled('academic_year')) {
                $query->whereYear('created_at', $request->academic_year);
            }

            $scores = $query->orderBy('created_at', 'desc')->get();

            // Calculate statistics
            $totalCredits = 0;
            $totalGradePoints = 0;
            $completedCourses = 0;
            $graduatedCourses = $scores->where('is_graduated', true)->count();

            foreach ($scores as $score) {
                if ($score->model_type === Course::class) {
                    $credits = $this->getCourseCredits($score->model);
                    $totalCredits += $credits;
                    $totalGradePoints += ($score->value * $credits);
                    $completedCourses++;
                } elseif ($score->model_type === Bundle::class) {
                    foreach ($score->model->courses as $course) {
                        $credits = $this->getCourseCredits($course);
                        $totalCredits += $credits;
                        $totalGradePoints += ($score->value * $credits);
                        $completedCourses++;
                    }
                }
            }

            $gpa = $totalCredits > 0 ? round($totalGradePoints / $totalCredits, 2) : 0;

            return response()->json([
                'success' => true,
                'message' => 'Student transcript retrieved successfully',
                'data' => [
                    'student' => [
                        'id' => $student->id,
                        'name' => $student->name,
                        'email' => $student->email,
                    ],
                    'transcript' => TranscriptResource::collection($scores),
                    'summary' => [
                        'total_credits' => $totalCredits,
                        'completed_courses' => $completedCourses,
                        'graduated_courses' => $graduatedCourses,
                        'gpa' => $gpa,
                        'total_scores' => $scores->count()
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve student transcript',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get course credits from metadata or default value
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

        // Default to 3 credits if not specified
        return 3;
    }

    /**
     * Get transcript summary statistics
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function summary(Request $request): JsonResponse
    {
        try {
            $studentId = $request->get('student_id', Auth::id());

            $scores = Score::where('user_id', $studentId)
                ->with(['model'])
                ->get();

            $totalScores = $scores->count();
            $averageScore = $scores->avg('value');
            $highestScore = $scores->max('value');
            $lowestScore = $scores->min('value');
            $graduatedCount = $scores->where('is_graduated', true)->count();
            $graduationRate = $totalScores > 0 ? round(($graduatedCount / $totalScores) * 100, 2) : 0;

            // Group by semester
            $semesterStats = $scores->groupBy('batches')->map(function ($semesterScores) {
                return [
                    'total_courses' => $semesterScores->count(),
                    'average_score' => round($semesterScores->avg('value'), 2),
                    'graduated_courses' => $semesterScores->where('is_graduated', true)->count()
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Transcript summary retrieved successfully',
                'data' => [
                    'overall_statistics' => [
                        'total_scores' => $totalScores,
                        'average_score' => round($averageScore, 2),
                        'highest_score' => $highestScore,
                        'lowest_score' => $lowestScore,
                        'graduated_courses' => $graduatedCount,
                        'graduation_rate' => $graduationRate
                    ],
                    'semester_statistics' => $semesterStats
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to retrieve transcript summary',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
