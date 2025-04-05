<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\CategoryController;
use App\Http\Controllers\API\CommentController;
use App\Http\Controllers\API\CourseController;
use App\Http\Controllers\API\GetInTouchController;
use App\Http\Controllers\API\InstructorController;
use App\Http\Controllers\API\NewsController;
use App\Http\Controllers\API\QuestionAndAnswerCategoryController;
use App\Http\Controllers\API\QuestionAndAnswerController;
use App\Http\Controllers\API\TagController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);

    Route::get('/verify-email/{id}', [AuthController::class, 'verifyEmail']);
    Route::post('/resend-verification', [AuthController::class, 'resendVerifyEmail']);

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
    Route::post('/reset-password', [AuthController::class, 'resetPassword']);
});

Route::resource('get-in-touches', GetInTouchController::class);
Route::resource('courses', CourseController::class);
Route::resource('instructors', InstructorController::class);
Route::resource('news', NewsController::class);
Route::resource('question-and-answers', QuestionAndAnswerController::class);
Route::resource('question-and-answer-categories', QuestionAndAnswerCategoryController::class);

Route::prefix('courses')->name('courses.')->group(function () {
    Route::prefix('{course}')->group(function () {
        Route::get('/sections', [CourseController::class, 'sections'])->name('sections');
        Route::get('/reviews', [CourseController::class, 'reviews'])->name('reviews');
        Route::get('/comments', [CourseController::class, 'comments'])->name('comments');
    });
});

Route::resource('categories', CategoryController::class);
Route::prefix('categories')->name('categories.')->group(function () {
    Route::prefix('{category}')->group(function () {
        Route::get('/courses', [CategoryController::class, 'courses'])->name('courses');
    });
});

Route::resource('tags', TagController::class);
Route::prefix('tags')->name('tags.')->group(function () {
    Route::prefix('{tag}')->group(function () {
        Route::get('/courses', [TagController::class, 'courses'])->name('courses');
    });
});

Route::prefix('comments')->group(function () {
    Route::get('/', [CommentController::class, 'index']);
    Route::prefix('{comment}')->group(function () {
        Route::get('/', [CommentController::class, 'show']);
        Route::get('/comments', [CommentController::class, 'comments']);
    });
});

Route::prefix('protected')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/logout', [AuthController::class, 'logout']);
    });

    Route::resource('courses', CourseController::class);
    Route::prefix('courses')->group(function () {
        Route::prefix('{course}')->group(function () {
            Route::get('/likes', [CourseController::class, 'likes']);
            Route::get('/reviews', [CourseController::class, 'reviews']);
            Route::get('/comments', [CourseController::class, 'comments']);
            Route::post('/reviews', [CourseController::class, 'storeReview']);
            Route::post('/comments', [CourseController::class, 'storeComment']);
            Route::post('/likes', [CourseController::class, 'storeLike']);
        });
    });

    Route::resource('instructors', InstructorController::class);
    Route::prefix('instructors')->group(function () {
        Route::prefix('{instructor}')->group(function () {
            Route::get('/followers', [InstructorController::class, 'followers']);
            Route::post('/followers', [InstructorController::class, 'storeFollower']);
        });
    });


    Route::resource('categories', CategoryController::class);
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::prefix('{category}')->group(function () {
            Route::get('/courses', [CategoryController::class, 'courses'])->name('courses');
        });
    });

    Route::resource('tags', TagController::class);
    Route::prefix('tags')->name('tags.')->group(function () {
        Route::prefix('{tag}')->group(function () {
            Route::get('/courses', [TagController::class, 'courses'])->name('courses');
        });
    });

    Route::resource('news', NewsController::class);
    Route::prefix('news')->group(function () {
        Route::prefix('{news}')->group(function () {
            Route::get('/likes', [NewsController::class, 'likes']);
            Route::get('/comments', [NewsController::class, 'comments']);
            Route::post('/likes', [NewsController::class, 'storeLike']);
            Route::post('/comments', [NewsController::class, 'storeComment']);
        });
    });

    Route::prefix('comments')->group(function () {
        Route::post('/store', [CommentController::class, 'store']);
        Route::prefix('{comment}')->group(function () {
            Route::get('/', [CommentController::class, 'show']);
            Route::put('/', [CommentController::class, 'update']);
        });
    });
});
