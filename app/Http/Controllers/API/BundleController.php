<?php

namespace App\Http\Controllers\API;

use App\Models\Bundle;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Http\Resources\BundleResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\ActiveBundleWithCoursesResource;
use App\Models\TransactionDetail;
use Illuminate\Support\Facades\Cache;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Validation\ValidationException;

class BundleController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $bundlings = Bundle::active()->paginate($request->input('limit', 10));
            return $this->success(data: BundleResource::collection($bundlings), paginate: $bundlings);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function courses(Request $request, string $slug)
    {
        try {
            $bundling = Bundle::where('slug', $slug)->first();
            if (!$bundling) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'bundling id'])]);
            }
            $courses = $bundling->courses()->paginate($request->input('limit', 10));
            return $this->success(data: CourseResource::collection($courses), paginate: $courses);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        try {
            $bundling = Bundle::where('slug', $slug)->first();
            if (!$bundling) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'bundling id'])]);
            }
            if ($request->user()) {
                $viewer = $bundling->viewers()
                    ->whereDate('created_at', Carbon::today())
                    ->where('user_id', $request->user()->id)
                    ->first();
                if (!$viewer) {
                    $bundling->viewers()->create([
                        'user_id' => $request->user()->id
                    ]);
                }
            }
            return $this->success(data: new BundleResource($bundling));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    /**
     * Display a listing of active bundles that have courses relationship.
     */
    public function activeBundlesWithCourses(Request $request)
    {
        try {
            // Validate request parameters
            $request->validate([
                'limit' => 'nullable|integer|min:1|max:100',
                'search' => 'nullable|string|max:255',
                'sort_by' => 'nullable|string|in:name,created_at,updated_at',
                'sort_order' => 'nullable|string|in:asc,desc'
            ]);
            
            // Create cache key based on filters and user ID
            $cacheKey = 'purchased_bundles_with_courses_' . $request->user()->id . '_' . md5(serialize($request->all()));
            
            // Try to get from cache first
            $result = Cache::remember($cacheKey, 300, function () use ($request) {
                // Get bundle IDs that user has successfully purchased
                $purchasedBundleIds = TransactionDetail::whereHasMorph('model', [Bundle::class])
                    ->whereHas('transaction', function ($q) use ($request) {
                        $q->where('user_id', $request->user()->id)
                          ->where('status', 'success');
                    })
                    ->pluck('model_id')
                    ->unique();
                
                // Query active bundles that have courses and user has purchased
                $query = Bundle::where('status', true) // Only active bundles
                    ->whereIn('id', $purchasedBundleIds) // Only purchased bundles
                    ->whereHas('courses', function ($q) {
                        $q->where('status', true); // Only active courses
                    })
                    ->with([
                        'courses' => function ($q) {
                            $q->select(['id', 'name', 'slug', 'description', 'short_description', 'duration', 'duration_units', 'level', 'language', 'is_paid', 'user_id'])
                              ->where('status', true)
                              ->with(['price', 'metadata', 'user:id,name']);
                        },
                        'products' => function ($q) {
                            $q->select(['id', 'name', 'slug', 'description'])
                              ->with(['price', 'metadata']);
                        },
                        'price',
                        'metadata'
                    ]);
                
                // Apply search filter
                if ($request->filled('search')) {
                    $search = $request->search;
                    $query->where(function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                          ->orWhere('description', 'like', "%{$search}%")
                          ->orWhere('short_description', 'like', "%{$search}%");
                    });
                }
                
                // Apply sorting
                $sortBy = $request->input('sort_by', 'created_at');
                $sortOrder = $request->input('sort_order', 'desc');
                $query->orderBy($sortBy, $sortOrder);
                
                // Paginate results
                $limit = $request->input('limit', 10);
                return $query->paginate($limit);
            });
            
            return $this->success(
                data: ActiveBundleWithCoursesResource::collection($result),
                paginate: $result
            );
            
        } catch (ValidationException $e) {
            return $this->error('Validation failed', 422, $e->errors());
        } catch (\Throwable $th) {
            return $this->error('An error occurred while fetching active bundles with courses', 500);
        }
    }

    /**
     * Generate PDF report for courses in a bundle
     */
    public function generateCoursesReportPDF(Request $request, string $slug)
    {
        try {
            // Validate that user has purchased this bundle
            $bundle = Bundle::where('slug', $slug)
                ->where('status', true)
                ->with([
                    'courses' => function ($query) use ($request) {
                        $query->where('status', true)
                            ->with([
                                'user:id,name,email',
                                'price',
                                'metadata',
                                'sections.lessons' => function ($lessonQuery) use ($request) {
                                    $lessonQuery->with([
                                        'score' => function ($scoreQuery) use ($request) {
                                            $scoreQuery->where('user_id', $request->user()->id);
                                        }
                                    ]);
                                }
                            ]);
                    },
                    'price',
                    'metadata'
                ])
                ->first();

            if (!$bundle) {
                return $this->error('Bundle not found', 404);
            }

            // Check if user has purchased this bundle
            $hasPurchased = TransactionDetail::whereHasMorph('model', [Bundle::class], function ($query) use ($bundle) {
                    $query->where('model_id', $bundle->id);
                })
                ->whereHas('transaction', function ($q) use ($request) {
                    $q->where('user_id', $request->user()->id)
                      ->where('status', 'success');
                })
                ->exists();

            if (!$hasPurchased) {
                return $this->error('You have not purchased this bundle', 403);
            }

            // Prepare data for PDF
            $reportData = [
                'bundle' => $bundle,
                'user' => $request->user(),
                'generated_at' => now()->format('d F Y H:i:s'),
                'courses_data' => $this->prepareCourseDataForPDF($bundle->courses, $request->user()->id)
            ];

            // Generate PDF
            $pdf = Pdf::loadView('pdf.courses-report', $reportData);
            $pdf->setPaper('A4', 'portrait');

            $filename = 'courses-report-' . $bundle->slug . '-' . now()->format('Y-m-d') . '.pdf';

            return $pdf->download($filename);

        } catch (\Throwable $th) {
            return $this->error('An error occurred while generating PDF report', 500);
        }
    }

    /**
     * Prepare course data for PDF report
     */
    private function prepareCourseDataForPDF($courses, $userId)
    {
        return $courses->map(function ($course) use ($userId) {
            $totalLessons = 0;
            $completedLessons = 0;
            $totalScore = 0;
            $scoreCount = 0;
            $averageScore = 0;

            foreach ($course->sections as $section) {
                foreach ($section->lessons as $lesson) {
                    $totalLessons++;
                    
                    if ($lesson->score && $lesson->score->user_id == $userId) {
                        $completedLessons++;
                        $totalScore += $lesson->score->value;
                        $scoreCount++;
                    }
                }
            }

            if ($scoreCount > 0) {
                $averageScore = round($totalScore / $scoreCount, 2);
            }

            $completionPercentage = $totalLessons > 0 ? round(($completedLessons / $totalLessons) * 100, 2) : 0;

            return [
                'id' => $course->id,
                'name' => $course->name,
                'description' => $course->description,
                'instructor' => $course->user?->name ?? 'Unknown',
                'level' => $course->level,
                'language' => $course->language,
                'duration' => $course->duration,
                'duration_units' => $course->duration_units,
                'total_lessons' => $totalLessons,
                'completed_lessons' => $completedLessons,
                'completion_percentage' => $completionPercentage,
                'average_score' => $averageScore,
                'score_count' => $scoreCount,
                'price' => [
                    'amount' => $course->price?->amount ?? 0,
                    'currency' => $course->price?->currency ?? 'IDR'
                ],
                'requirements' => $course->metadata?->requirements ?? [],
                'learning_outcomes' => $course->metadata?->learning_outcomes ?? []
            ];
        })->toArray();
    }
}
