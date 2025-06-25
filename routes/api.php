<?php

use App\Http\Controllers\API\AppointmentController;
use App\Http\Controllers\API\AreaController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\TagController;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CartController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\BundleController;
use App\Http\Controllers\API\CouponController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\ReviewController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\WebhookController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\EnrollmentController;
use App\Http\Controllers\API\EventController;
use App\Http\Controllers\API\GetInTouchController;
use App\Http\Controllers\API\GoogleAuthController;
use App\Http\Controllers\API\InstructorController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\QuestionAndAnswerController;
use App\Http\Controllers\API\QuestionAndAnswerCategoryController;

Route::prefix('check')->name('check.')->group(function () {
    Route::name('env')->get('/', function () {
        Log::info('env APP_ENV: ' . env('APP_ENV'));
        Log::info('env FILESYSTEM_DISK: ' . env('FILESYSTEM_DISK'));
    });
});

Route::prefix('auth')->middleware(['throttle:3,1'])->name('auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::prefix('google')->name('google.')->group(function () {
        Route::post('/login', [GoogleAuthController::class, 'login'])->name('login');
    });
    Route::get('/verify-email/{id}', [AuthController::class, 'verifyEmail'])->name('verify');
    Route::post('/resend-verification', [AuthController::class, 'resendVerifyEmail'])->name('resend-verification');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
});

Route::prefix('webhooks')->name('webhooks.')->group(function () {
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::get('{gateway}', [WebhookController::class, 'receiveFromPayment'])->name('get.receive-from-payment');
        Route::post('{gateway}', [WebhookController::class, 'receiveFromPayment'])->name('post.receive-from-payment');
    });
});

Route::apiResource('get-in-touches', GetInTouchController::class)->only(['store']);
Route::apiResource('courses', CourseController::class)->only(['index', 'show']);

Route::apiResource('instructors', InstructorController::class)->only(['index', 'show']);
Route::prefix('instructors')->name('instructors.')->group(function () {
    Route::prefix('{instructor}')->group(function () {
        Route::get('/followers', [InstructorController::class, 'followers'])->name('followers');
    });
});

Route::apiResource('news', NewsController::class)->only(['index', 'show']);
Route::apiResource('question-and-answers', QuestionAndAnswerController::class)->only(['index', 'show']);
Route::apiResource('question-and-answer-categories', QuestionAndAnswerCategoryController::class)->only(['index', 'show']);
Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);

Route::prefix('provinces')->name('provinces.')->group(function () {
    Route::get('/', [AreaController::class, 'provinces'])->name('index');
    Route::prefix('{province}')->group(function () {
        Route::prefix('regencies')->name('regencies.')->group(function () {
            Route::get('/', [AreaController::class, 'regencies'])->name('index');
            Route::prefix('{regency}')->group(function () {
                Route::prefix('districts')->name('districts.')->group(function () {
                    Route::get('/', [AreaController::class, 'districts'])->name('index');
                    Route::prefix('{district}')->group(function () {
                        Route::prefix('villages')->name('villages.')->group(function () {
                            Route::get('/', [AreaController::class, 'villages'])->name('index');
                        });
                    });
                });
            });
        });
    });
});

Route::prefix('courses')->name('courses.')->group(function () {
    Route::prefix('{course}')->group(function () {
        Route::get('/sections', [CourseController::class, 'sections'])->name('sections');
        Route::get('/reviews', [CourseController::class, 'reviews'])->name('reviews');
        Route::get('/comments', [CourseController::class, 'comments'])->name('comments');
    });
});

Route::apiResource('bundles', BundleController::class)->only(['index', 'show']);

Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
Route::prefix('categories')->name('categories.')->group(function () {
    Route::prefix('{category}')->group(function () {
        Route::get('/courses', [CategoryController::class, 'courses'])->name('courses');
    });
});

Route::apiResource('tags', TagController::class)->only(['index', 'show']);
Route::prefix('tags')->name('tags.')->group(function () {
    Route::prefix('{tag}')->group(function () {
        Route::get('/courses', [TagController::class, 'courses'])->name('courses');
    });
});

Route::apiResource('comments', CommentController::class)->only(['index', 'show']);
Route::prefix('comments')->group(function () {
    Route::prefix('{comment}')->group(function () {
        Route::get('/comments', [CommentController::class, 'comments']);
    });
});

Route::prefix('protected')->middleware(['auth:sanctum'])->name('protected.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/resend-verification', [AuthController::class, 'resendVerifyEmail'])
            ->middleware(['throttle:3,1'])
            ->name('resend-verification');
        Route::post('/change-password', [AuthController::class, 'resetPassword'])->name('reset-password');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::apiResource('users', UserController::class)->only(['update']);
    Route::prefix('users')->name('users.')->group(function () {
        Route::prefix('{user}')->group(function () {
            Route::put('/update-avatar', [UserController::class, 'updateAvatar'])->name('update.avatar');
            Route::get('/following', [UserController::class, 'following'])->name('following');
            Route::get('/certificates', [UserController::class, 'certificates'])->name('certificates');
            Route::get('/followers', [UserController::class, 'followers'])->name('followers');
            Route::prefix('addresses')->name('addresses.')->group(function () {
                Route::get('/', [UserController::class, 'addresses'])->name('index');
                Route::post('/', [UserController::class, 'storeAddress'])->name('store');
                Route::prefix('{address}')->group(function () {
                    Route::get('/', [UserController::class, 'showAddress'])->name('show');
                    Route::put('/', [UserController::class, 'updateAddress'])->name('update');
                    Route::delete('/', [UserController::class, 'destroyAddress'])->name('destroy');
                });
            });
        });
    });

    Route::apiResource('bundles', BundleController::class)->only(['index', 'show']);

    Route::apiResource('courses', CourseController::class)->only(['index', 'show']);
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::prefix('{course}')->group(function () {
            Route::get('/announcements', [CourseController::class, 'announcements'])->name('announcements');
            Route::get('/lessons', [CourseController::class, 'searchLessons'])->name('lessons');
            Route::get('/likes', [CourseController::class, 'likes'])->name('likes');
            Route::get('/reviews', [CourseController::class, 'reviews'])->name('reviews');
            Route::get('/coupons', [CourseController::class, 'coupons'])->name('coupons');
            Route::get('/comments', [CourseController::class, 'comments'])->name('comments');
            Route::middleware(['throttle:3,1'])->group(function () {
                Route::prefix('reviews')->name('reviews.')->group(function () {
                    Route::post('/', [CourseController::class, 'storeReview'])->name('store');
                    Route::put('/{review}', [CourseController::class, 'updateReview'])->name('update');
                });
                Route::post('/comments', [CourseController::class, 'storeComment'])->name('store.comments');
                Route::post('/likes', [CourseController::class, 'storeLike'])->name('store.likes');
                Route::post('/certificates', [CourseController::class, 'storeCertificate'])->name('store.certificates');
            });

            Route::prefix('sections')->name('sections.')->group(function () {
                Route::get('/', [CourseController::class, 'sections'])->name('index');
                Route::prefix('{section}')->group(function () {
                    Route::get('/', [CourseController::class, 'showSection'])->name('show');
                    Route::prefix('lessons')->name('lessons.')->group(function () {
                        Route::get('/', [CourseController::class, 'lessons'])->name('index');
                        Route::prefix('{lesson}')->group(function () {
                            Route::get('/', [CourseController::class, 'showLesson'])->name('show');
                            Route::prefix('quizzes')->name('quizzes.')->group(function () {
                                Route::get('/', [CourseController::class, 'quizzes'])->name('index');
                                Route::prefix('{quiz}')->group(function () {
                                    Route::get('/', [CourseController::class, 'showQuiz'])->name('show');
                                    Route::prefix('options')->name('options.')->group(function () {
                                        Route::get('/', [CourseController::class, 'options'])->name('index');
                                        Route::prefix('{option}')->group(function () {
                                            Route::get('/', [CourseController::class, 'showOption'])->name('show');
                                        });
                                    });
                                    Route::post('answers', [CourseController::class, 'storeQuizAnswer'])->name('store.answers');
                                });
                            });
                            Route::middleware(['throttle:3,1'])->group(function () {
                                Route::post('answers', [CourseController::class, 'storeLessonAnswer'])->name('answers.notes');
                                Route::prefix('notes')->name('notes.')->group(function () {
                                    Route::get('/', [CourseController::class, 'lessonNotes'])->name('index');
                                    Route::post('/', [CourseController::class, 'storeLessonNote'])->name('store');
                                    Route::put('/{note}', [CourseController::class, 'updateLessonNote'])->name('update');
                                    Route::delete('/{note}', [CourseController::class, 'destroyLessonNote'])->name('destroy');
                                });
                                Route::post('score-quiz-answer', [CourseController::class, 'storeScoreQuizAnswer'])->name('score-quiz-answer.store');
                                Route::post('progress', [CourseController::class, 'storeLessonProgress'])->name('progress');
                                Route::post('likes', [CourseController::class, 'storeLikeLesson'])->name('likes');
                                Route::post('appointments', [CourseController::class, 'storeAppointmentLesson'])->name('appointments');
                            });
                        });
                    });
                });
            });
        });
    });

    Route::apiResource('instructors', InstructorController::class)->only(['index', 'show']);
    Route::prefix('instructors')->name('instructors.')->group(function () {
        Route::prefix('{instructor}')->group(function () {
            Route::get('/followers', [InstructorController::class, 'followers'])->name('followers');
            Route::post('/followers', [InstructorController::class, 'storeFollower'])->middleware(['throttle:3,1'])->name('store.followers');
        });
    });

    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::prefix('{category}')->group(function () {
            Route::get('/courses', [CategoryController::class, 'courses'])->name('courses');
        });
    });

    Route::apiResource('tags', TagController::class)->only(['index', 'show']);
    Route::prefix('tags')->name('tags.')->group(function () {
        Route::prefix('{tag}')->group(function () {
            Route::get('/courses', [TagController::class, 'courses'])->name('courses');
        });
    });

    Route::apiResource('news', NewsController::class)->only(['index', 'show']);
    Route::prefix('news')->name('news.')->group(function () {
        Route::prefix('{news}')->group(function () {
            Route::get('/likes', [NewsController::class, 'likes'])->name('likes');
            Route::get('/comments', [NewsController::class, 'comments'])->name('comments');
            Route::middleware(['throttle:3,1'])->group(function () {
                Route::post('/likes', [NewsController::class, 'storeLike'])->name('store.likes');
                Route::post('/comments', [NewsController::class, 'storeComment'])->name('store.comments');
            });
        });
    });

    Route::apiResource('comments', CommentController::class)->only(['index', 'show']);
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::prefix('{comment}')->group(function () {
            Route::get('/comments', [CommentController::class, 'comments'])->name('comments');
        });
    });

    Route::apiResource('carts', CartController::class);

    Route::prefix('enrollments')->middleware(['throttle:3,1'])->name('enrollments.')->group(function () {
        Route::post('/course', [EnrollmentController::class, 'storeEnrollmentCourse'])->name('course.store');
        Route::post('/event', [EnrollmentController::class, 'storeEnrollmentEvent'])->name('event.store');
    });

    Route::apiResource('transactions', TransactionController::class)->except(['destroy']);
    Route::prefix('transactions')->name('transactions.')->group(function () {
        Route::prefix('{transaction}')->group(function () {
            Route::post('/payment', [TransactionController::class, 'payment'])->name('payment');
            Route::get('/payment-channels', [TransactionController::class, 'paymentChannels'])->name('payment-channels');
        });
    });

    Route::apiResource('reviews', ReviewController::class)->only(['index', 'show']);
    Route::apiResource('coupons', CouponController::class)->only(['index', 'show']);
    Route::apiResource('appointments', AppointmentController::class)->only(['index', 'show']);
    Route::apiResource('events', EventController::class)->only(['index', 'show']);
});
