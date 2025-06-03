<?php

namespace App\Http\Controllers\API;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\QuizResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\CouponResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LessonResource;
use App\Http\Resources\OptionResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\CommentResource;
use App\Http\Resources\SectionResource;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Course\StoreReviewRequest;
use App\Http\Requests\Course\StoreCommentRequest;

class CourseController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $courses = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->active();
            if ($request->user() && $request->input('mine')) {
                $courses = $courses->whereHas('transactions', fn(Builder $q) => $q->where('user_id', $request->user()->id));
            }
            if ($request->search) {
                $courses = $courses->where('name', 'ilike', "%$request->search%");
            }
            $courses = $courses
                ->whereHas('lessons', fn(Builder $q) => $q->where('lessons.status', true))
                ->paginate($request->input('limit', 10));
            return $this->success(data: CourseResource::collection($courses), paginate: $courses);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function reviews(Request $request, string $slug)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $reviews = $course->reviews()->paginate($request->input('limit', 10));
            return $this->success(data: ReviewResource::collection($reviews), paginate: $reviews);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function coupons(Request $request, string $slug)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $coupons = $course->coupons()->paginate($request->input('limit', 10));
            return $this->success(data: CouponResource::collection($coupons), paginate: $coupons);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function sections(Request $request, string $slug)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $sections = $course->sections()->active()->paginate($request->input('limit', 10));
            return $this->success(data: SectionResource::collection($sections), paginate: $sections);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function lessons(Request $request, string $slug, string $section_id)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->sections()->active()->where('id', $section_id)->first();
            if (!$section) {
                throw ValidationException::withMessages(['section_id' => trans('validation.exists', ['attribute' => 'section id'])]);
            }
            $lessons = $section->lessons()->active()->paginate($request->input('limit', 10));
            return $this->success(data: LessonResource::collection($lessons), paginate: $lessons);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function quizzes(Request $request, string $slug, string $section_id, string $lesson_id)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->sections()->active()->where('id', $section_id)->first();
            if (!$section) {
                throw ValidationException::withMessages(['section_id' => trans('validation.exists', ['attribute' => 'section id'])]);
            }
            $lesson = $section->lessons()->where('id', $lesson_id)->first();
            if (!$lesson) {
                throw ValidationException::withMessages(['lesson_id' => trans('validation.exists', ['attribute' => 'lesson id'])]);
            }
            $quizzes = $lesson->quizzes()->paginate($request->input('limit', 10));
            return $this->success(data: QuizResource::collection($quizzes), paginate: $quizzes);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function options(Request $request, string $slug, string $section_id, string $lesson_id, string $quiz_id)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->sections()->active()->where('id', $section_id)->first();
            if (!$section) {
                throw ValidationException::withMessages(['section_id' => trans('validation.exists', ['attribute' => 'section id'])]);
            }
            $lesson = $section->lessons()->where('id', $lesson_id)->first();
            if (!$lesson) {
                throw ValidationException::withMessages(['lesson_id' => trans('validation.exists', ['attribute' => 'lesson id'])]);
            }
            $quiz = $lesson->quizzes()->where('id', $quiz_id)->first();
            if (!$quiz) {
                throw ValidationException::withMessages(['quiz_id' => trans('validation.exists', ['attribute' => 'quiz id'])]);
            }
            $options = $quiz->options()->paginate($request->input('limit', 10));
            return $this->success(data: OptionResource::collection($options), paginate: $options);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function comments(Request $request, string $slug)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $comments = $course->comments()->paginate($request->input('limit', 10));
            return $this->success(data: CommentResource::collection($comments), paginate: $comments);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display a listing of the resource.
     */
    public function latestSection(Request $request, string $slug)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->latestSection;
            return $this->success(data: new SectionResource($section));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    // latestSection

    /**
     * Display a listing of the resource.
     */
    public function likes(Request $request, string $slug)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $users = $course->likes()->paginate($request->input('limit', 10));
            return $this->success(data: UserResource::collection($users), paginate: $users);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeReview(StoreReviewRequest $request, string $slug)
    {
        try {
            DB::beginTransaction();
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }

            $transaction = $course->transactions()->where('user_id', $request->user()->id)->first();

            if (!$transaction) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }

            $review = $course->reviews()->create([
                'rating' => $request->rating,
                'description' => $request->description,
                'transaction_detail_id' => $transaction->pivot->id,
                'user_id' => $request->user()->id,
            ]);
            DB::commit();
            return $this->success(data: new ReviewResource($review), status: 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeComment(StoreCommentRequest $request, string $slug)
    {
        try {
            DB::beginTransaction();
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $transaction = $course->transactions()->where('user_id', $request->user_id)->first();

            if (!$transaction) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'transaction id'])]);
            }

            $comment = $course->comments()->create([
                'description' => $request->description,
                'user_id' => $request->user()->id,
            ]);
            DB::commit();
            return $this->success(data: new CommentResource($comment), status: 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeLike(Request $request, string $slug)
    {
        try {
            DB::beginTransaction();
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }

            $request->user()->likeCourses()->toggle($course->id);

            DB::commit();
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            return $this->success(data: new CourseResource($course), status: 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeCertificate(Request $request, string $slug)
    {
        try {
            DB::beginTransaction();

            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course'])]);
            }

            $enrollment = $course->enrollments()->where('user_id', $request->user()->id)->orderBy('created_at', 'desc')->first();
            if (!$enrollment) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'enrollment'])]);
            }

            if ($this->hasGraduated($request->user()->id, $course)) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'graduated'])]);
            }

            $certificate = $enrollment->certificate()
                ->orderBy('created_at', 'desc')->first();
            if (!$certificate) {
                $certificate->certificate_number = Str::upper(env('APP_NAME', "Online Course")) . "-" . Str::upper(Str::random(4)) . "-" . date('ddMMYYYY');
                $certificate->issue_date = now();
                if ($certificate->hasMedia('certificates')) {
                    // Generate PDF using blade
                    $pdf = Pdf::loadView('pdf.certificate', compact('certificate'))
                        ->setPaper('a4', 'landscape')->setWarnings(false)->output();
                    $certificate
                        ->addMediaFromString($pdf)
                        ->usingFileName(Str::slug($request->user()->id . '_' . $certificate->certificate_number, '_'))
                        ->toMediaCollection('certificates', 's3');
                }
                $certificate->save();
            }

            DB::commit();
            return $this->success(data: new CourseResource($course), status: 200);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    // storeCertificate

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            return $this->success(data: new CourseResource($course));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function showSection(Request $request, string $slug, string $section_id)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->sections()->active()->where('id', $section_id)->first();
            if (!$section) {
                throw ValidationException::withMessages(['section_id' => trans('validation.exists', ['attribute' => 'section id'])]);
            }
            return $this->success(data: new SectionResource($section));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function showLesson(Request $request, string $slug, string $section_id, string $lesson_id)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->sections()->active()->where('id', $section_id)->first();
            if (!$section) {
                throw ValidationException::withMessages(['section_id' => trans('validation.exists', ['attribute' => 'section id'])]);
            }
            $lesson = $section->lessons()->where('id', $lesson_id)->first();
            if (!$lesson) {
                throw ValidationException::withMessages(['lesson_id' => trans('validation.exists', ['attribute' => 'lesson id'])]);
            }
            return $this->success(data: new LessonResource($lesson));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function showQuiz(Request $request, string $slug, string $section_id, string $lesson_id, string $quiz_id)
    {
        try {
            $course = Course::with(['latestSection.lessons', 'sections.lessons', 'reviews', 'enrollments'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->sections()->active()->where('id', $section_id)->first();
            if (!$section) {
                throw ValidationException::withMessages(['section_id' => trans('validation.exists', ['attribute' => 'section id'])]);
            }
            $lesson = $section->lessons()->where('id', $lesson_id)->first();
            if (!$lesson) {
                throw ValidationException::withMessages(['lesson_id' => trans('validation.exists', ['attribute' => 'lesson id'])]);
            }
            $quiz = $lesson->quizzes()->where('id', $quiz_id)->first();
            if (!$quiz) {
                throw ValidationException::withMessages(['quiz_id' => trans('validation.exists', ['attribute' => 'quiz id'])]);
            }
            return $this->success(data: new QuizResource($quiz));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    private function hasGraduated(string $user_id, Course $course): bool
    {
        $lessons = $course->lessons()->active();
        $total_lessons = $lessons->count();
        $completed_lessons = $lessons->progress()->where('user_id', $user_id)->count();

        return $lessons > 0 && $total_lessons == $completed_lessons;
    }
}
