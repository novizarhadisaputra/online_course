<?php

namespace App\Http\Controllers\API;

use App\Models\Course;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Carbon;
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
use App\Http\Resources\AnnouncementResource;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\Course\StoreReviewRequest;
use App\Http\Requests\Course\StoreCommentRequest;
use App\Http\Requests\Course\UpdateReviewRequest;
use App\Http\Requests\Course\StoreQuizAnswerRequest;
use App\Http\Requests\Course\StoreLessonAnswerRequest;
use App\Http\Requests\Course\StoreLessonProgressRequest;
use App\Http\Requests\Course\StoreAppointmentLessonRequest;

class CourseController extends Controller
{
    use ResponseTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $courses = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->active();
            if ($request->user() && $request->input('mine')) {
                $courses = $courses->whereHas('transactions', fn(Builder $q) => $q->where('user_id', $request->user()->id));
            }
            if ($request->search) {
                $courses = $courses->where('name', 'ilike', "%$request->search%");
            }
            if ($request->filter) {
                if (isset($request->filter['prices'])) {
                    if (Str::contains(implode(' ', $request->filter['prices']), ['paid', 'free'])) {
                        $courses = $courses->where('is_paid', '<>', null);
                    } else {
                        $is_paid = Str::contains(implode(' ', $request->filter['prices']), ['paid']);
                        $courses = $courses->where('is_paid', $is_paid);
                    }
                }
                if (isset($request->filter['levels'])) {
                    $courses = $courses->where('level', $request->filter['levels']);
                }
                if (isset($request->filter['categories'])) {
                    $courses = $courses->whereHas('category', fn(Builder $q) => $q->whereIn('name', $request->filter['categories']));
                }
                // if (isset($request->filter['ratings'])) {
                //     $courses = $courses->withAvg('reviews', 'rating');
                //     $courses = $courses->having(function ($q) use ($request) {
                //         foreach ($request->filter['ratings'] as $i => $value) {
                //             if ($i === 0) {
                //                 $q->where('reviews_avg_rating', '=', $value);
                //             } else {
                //                 $q->orWhere('reviews_avg_rating', '=', $value);
                //             }
                //         }
                //     });
                // }
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
    public function announcements(Request $request, string $slug)
    {
        try {
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers', 'announcements'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $announcements = $course->announcements()->paginate($request->input('limit', 10));
            return $this->success(data: AnnouncementResource::collection($announcements), paginate: $announcements);
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
    public function searchLessons(Request $request, string $slug)
    {
        try {
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $lessons = $course->lessons()->where('lessons.status', true);
            if ($request->search) {
                $lessons = $lessons->where('name', 'ilike', "%$request->search%");
            }
            $lessons = $lessons->paginate($request->input('limit', 10));
            return $this->success(data: LessonResource::collection($lessons), paginate: $lessons);
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->sections()->active()->where('id', $section_id)->first();
            if (!$section) {
                throw ValidationException::withMessages(['section_id' => trans('validation.exists', ['attribute' => 'section id'])]);
            }
            $lesson = $section->lessons()->with(['quizzes'])->where('id', $lesson_id)->first();
            if (!$lesson) {
                throw ValidationException::withMessages(['lesson_id' => trans('validation.exists', ['attribute' => 'lesson id'])]);
            }
            $quizzes = $lesson->quizzes()->with(['answer', 'user', 'options'])->paginate($request->input('limit', 10));

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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $section = $course->sections()->active()->where('id', $section_id)->first();
            if (!$section) {
                throw ValidationException::withMessages(['section_id' => trans('validation.exists', ['attribute' => 'section id'])]);
            }
            $lesson = $section->lessons()->with(['quizzes'])->where('id', $lesson_id)->first();
            if (!$lesson) {
                throw ValidationException::withMessages(['lesson_id' => trans('validation.exists', ['attribute' => 'lesson id'])]);
            }
            $quiz = $lesson->quizzes()->with(['answer', 'user', 'options'])->where('id', $quiz_id)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            $comments = $course->comments()->paginate($request->input('limit', 10));
            return $this->success(data: CommentResource::collection($comments), paginate: $comments);
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }

            $request->user()->likeCourses()->toggle($course->id);

            DB::commit();
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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

            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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

            $certificate = $enrollment->certificate()->orderBy('created_at', 'desc')->first();
            if (!$certificate) {
                $certificate->certificate_number = Str::upper(env('APP_NAME', "Online Course")) . "-" . Str::upper(Str::random(4)) . "-" . date('ddMMYYYY');
                $certificate->issue_date = now();
                if ($certificate->hasMedia('certificates')) {
                    // Generate PDF using blade
                    $pdf = Pdf::loadView('pdf.certificate', compact('certificate'))
                        ->setPaper('a4', 'landscape')->setWarnings(false)->output();
                    $certificate
                        ->addMediaFromString($pdf)
                        ->usingFileName(Str::slug($request->user()->id . '_' . $certificate->certificate_number, '_') . '.pdf')
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

    /**
     * Store a newly created resource in storage.
     */
    public function storeLikeLesson(Request $request, string $slug, string $section_id, string $lesson_id)
    {
        try {
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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

            $request->user()->likeLessons()->toggle($lesson->id);

            return $this->success(data: new LessonResource($lesson));
        } catch (\Throwable $th) {
            throw $th;
        }
    }


    /**
     * Store a newly created resource in storage.
     */
    public function storeAppointmentLesson(StoreAppointmentLessonRequest $request, string $slug, string $section_id, string $lesson_id)
    {
        try {
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $event = $lesson->events()->where('id', $request->event_id)->first();
            if (!$event) {
                throw ValidationException::withMessages(['event_id' => trans('validation.exists', ['attribute' => 'event id'])]);
            }
            $appointment = $event->appointments()->where('user_id', $request->user()->id)->first();
            if (!$appointment) {
                $event->appointments()->create([
                    'date' => $event->start_time,
                    'code' => Str::upper(Str::random()),
                    'is_attended' => $request->is_attended,
                    'user_id' => $request->user()->id,
                    'source_id' => $event->user_id,
                ]);
            } else {
                $appointment->date = $event->start_time;
                $appointment->code = Str::upper(Str::random());
                $appointment->is_attended = $request->is_attended;
                $appointment->user_id = $request->user()->id;
                $appointment->source_id = $event->user_id;
                $appointment->save();
            }

            return $this->success(data: new LessonResource($lesson));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function storeLessonProgress(StoreLessonProgressRequest $request, string $slug, string $section_id, string $lesson_id)
    {
        try {
            DB::beginTransaction();
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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

            if (!$lesson->progress) {
                $lesson->progress->create([
                    'data' => [
                        'seconds' => $request->seconds,
                        'page' => $request->page,
                    ],
                    'status' => $request->status,
                    'user_id' => $request->user()->id,
                ]);
            } else {
                $progress = $lesson->progress;
                $progress->data =  [
                    'seconds' => $request->seconds,
                    'page' => $request->page,
                ];
                $progress->status = $request->status;
                $progress->save();
            }
            $this->updateProgressCourse($request->user()->id, $course);
            DB::commit();
            return $this->success(data: new LessonResource($lesson));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function storeLessonAnswer(StoreLessonAnswerRequest $request, string $slug, string $section_id, string $lesson_id)
    {
        try {
            DB::beginTransaction();
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $answer = $lesson->answer()->where('user_id', $request->user()->id)->first();
            if ($request->hasFile('attachment')) {
                $lesson->clearMediaCollection('attachments');
                $lesson->addMediaFromRequest('attachment')
                    ->usingFileName($request->user()->id . '.png')
                    ->toMediaCollection('attachments', 's3');
            }
            if (!$answer) {
                $lesson->answer()->create([
                    'text' => $request->text,
                    'user_id' => $request->user()->id,
                ]);
            } else {
                $answer->text = $request->text;
                $answer->user_id = $request->user()->id;
                $answer->save();
            }
            DB::commit();
            return $this->success(data: new LessonResource($lesson));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    public function storeScoreQuizAnswer(Request $request, string $slug, string $section_id, string $lesson_id)
    {
        try {
            DB::beginTransaction();
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $qty = 0;
            foreach ($lesson->quizzes as $quiz) {
                $option = $quiz->options()->where(['id' => $quiz->answer->option_id, 'is_correct' => true])->first();
                if ($option) {
                    $qty++;
                }
            }
            $value =  ($qty / count($lesson->quizzes)) * 100;
            $score = $lesson->score()->where('user_id', $request->user()->id)->first();
            $is_graduated = true;
            if ($lesson->graduation_score > 0) {
                if ($score < $lesson->graduation_score) {
                    $is_graduated = false;
                }
            }
            if ($score) {
                $score->batches += 1;
                $score->value = $value;
                $score->is_graduated = $is_graduated;
                $score->save();
            } else {
                $lesson->score()->create([
                    'batches' => 1,
                    'value' => $value,
                    'user_id' => $request->user()->id,
                    'is_graduated' => $is_graduated
                ]);
            }

            return $this->success(data: new LessonResource($lesson));
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function storeQuizAnswer(StoreQuizAnswerRequest $request, string $slug, string $section_id, string $lesson_id, string $quiz_id)
    {
        try {
            DB::beginTransaction();
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $option = $quiz->options()->where('id', $request->option_id)->first();
            if (!$option) {
                throw ValidationException::withMessages(['option_id' => trans('validation.exists', ['attribute' => 'option id'])]);
            }
            $answer = $quiz->answer()->where('user_id', $request->user()->id)->first();
            if (!$answer) {
                $quiz->answer()->create([
                    'option_id' => $request->option_id,
                    'user_id' => $request->user()->id,
                ]);
            } else {
                $answer->option_id = $request->option_id;
                $answer->user_id = $request->user()->id;
                $answer->save();
            }

            DB::commit();
            return $this->success(data: new QuizResource($quiz));
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, string $slug)
    {
        try {
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }
            if ($request->user()) {
                $viewer = $course->viewers()
                    ->whereDate('created_at', Carbon::today())
                    ->where('user_id', $request->user()->id)
                    ->first();
                if ($viewer) {
                    $course->viewers()->create([
                        'user_id' => $request->user()->id
                    ]);
                }
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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
            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
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

    /**
     * Store a newly created resource in storage.
     */
    public function updateReview(UpdateReviewRequest $request, string $slug, string $review_id)
    {
        try {
            DB::beginTransaction();

            $course = Course::with(['sections.lessons', 'reviews', 'enrollments', 'viewers'])->where('slug', $slug)->first();
            if (!$course) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }

            $transaction = $course->transactions()->where('user_id', $request->user()->id)->first();
            if (!$transaction) {
                throw ValidationException::withMessages(['id' => trans('validation.exists', ['attribute' => 'course id'])]);
            }

            $review = $course->reviews()->where('id', $review_id)->first();
            if ($review) {
                $review->rating = $request->rating;
                $review->description = $request->description;
                $review->save();
            }

            DB::commit();
            return $this->success(data: new ReviewResource($review), status: 201);
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }

    private function hasGraduated(string $user_id, Course $course): bool
    {
        $progress = $this->checkProgress($user_id, $course);
        $total_lessons = $progress['total_lessons'];
        $completed_lessons = $progress['completed_lessons'];

        return $total_lessons > 0 && $total_lessons == $completed_lessons;
    }

    private function updateProgressCourse(string $user_id, Course $course)
    {
        $progress = $this->checkProgress($user_id, $course);
        $data = (object) [
            'percentage' => $progress['completed_lessons'] / $progress['completed_lessons'] * 100
        ];
        $status = $this->hasGraduated($user_id, $course);
        if (!$course->progress) {
            $course->progress->create([
                'data' => $data,
                'status' => $status,
                'user_id' => $user_id,
            ]);
        } else {
            $progress = $course->progress;
            $progress->data = $data;
            $progress->status = $status;
            $progress->save();
        }
    }

    private function checkProgress(string $user_id, Course $course)
    {
        $lessons = $course->lessons()->select(['lessons.id'])->active();
        $total_lessons = $lessons->count();
        $completed_lessons = $lessons
            ->progress()
            ->where(['user_id' => $user_id, 'status' => true])
            ->count();

        return compact('total_lessons', 'completed_lessons');
    }
}
