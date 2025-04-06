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
use App\Http\Controllers\API\UserController;

Route::prefix('auth')->name('auth.')->group(function () {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');

    Route::get('/verify-email/{id}', [AuthController::class, 'verifyEmail'])->name('verify');
    Route::post('/resend-verification', [AuthController::class, 'resendVerifyEmail'])->name('resend-verification');

    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot-password');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('reset-password');
});

Route::apiResource('get-in-touches', GetInTouchController::class);
Route::apiResource('courses', CourseController::class);
Route::apiResource('instructors', InstructorController::class);
Route::apiResource('news', NewsController::class);
Route::apiResource('question-and-answers', QuestionAndAnswerController::class);
Route::apiResource('question-and-answer-categories', QuestionAndAnswerCategoryController::class);

Route::prefix('courses')->name('courses.')->group(function () {
    Route::prefix('{course}')->group(function () {
        Route::get('/sections', [CourseController::class, 'sections'])->name('sections');
        Route::get('/reviews', [CourseController::class, 'reviews'])->name('reviews');
        Route::get('/comments', [CourseController::class, 'comments'])->name('comments');
    });
});

Route::apiResource('categories', CategoryController::class);
Route::prefix('categories')->name('categories.')->group(function () {
    Route::prefix('{category}')->group(function () {
        Route::get('/courses', [CategoryController::class, 'courses'])->name('courses');
    });
});

Route::apiResource('tags', TagController::class);
Route::prefix('tags')->name('tags.')->group(function () {
    Route::prefix('{tag}')->group(function () {
        Route::get('/courses', [TagController::class, 'courses'])->name('courses');
    });
});

Route::apiResource('comments', CommentController::class)->only([
    'index',
    'show'
]);
Route::prefix('comments')->group(function () {
    Route::prefix('{comment}')->group(function () {
        Route::get('/comments', [CommentController::class, 'comments']);
    });
});

Route::prefix('protected')->middleware(['auth:sanctum'])->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::get('/profile', [AuthController::class, 'profile'])->name('profile');
        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    });

    Route::apiResource('users', UserController::class)->only(['update']);
    Route::prefix('users')->name('users.')->group(function () {
        Route::prefix('{user}')->group(function () {
            Route::put('/update-avatar', [UserController::class, 'updateAvatar'])->name('update.avatar');
            Route::get('/following', [UserController::class, 'following'])->name('following');
            Route::get('/followers', [UserController::class, 'followers'])->name('followers');
        });
    });

    Route::apiResource('courses', CourseController::class);
    Route::prefix('courses')->name('courses.')->group(function () {
        Route::prefix('{course}')->group(function () {
            Route::get('/likes', [CourseController::class, 'likes'])->name('likes');
            Route::get('/reviews', [CourseController::class, 'reviews'])->name('reviews');
            Route::get('/comments', [CourseController::class, 'comments'])->name('comments');
            Route::post('/reviews', [CourseController::class, 'storeReview'])->name('store.reviews');
            Route::post('/comments', [CourseController::class, 'storeComment'])->name('store.comments');
            Route::post('/likes', [CourseController::class, 'storeLike'])->name('store.likes');
        });
    });

    Route::apiResource('instructors', InstructorController::class);
    Route::prefix('instructors')->name('instructors.')->group(function () {
        Route::prefix('{instructor}')->group(function () {
            Route::get('/followers', [InstructorController::class, 'followers'])->name('followers');
            Route::post('/followers', [InstructorController::class, 'storeFollower'])->name('store.followers');
        });
    });

    Route::apiResource('categories', CategoryController::class);
    Route::prefix('categories')->name('categories.')->group(function () {
        Route::prefix('{category}')->group(function () {
            Route::get('/courses', [CategoryController::class, 'courses'])->name('courses');
        });
    });

    Route::apiResource('tags', TagController::class);
    Route::prefix('tags')->name('tags.')->group(function () {
        Route::prefix('{tag}')->group(function () {
            Route::get('/courses', [TagController::class, 'courses'])->name('courses');
        });
    });

    Route::apiResource('news', NewsController::class);
    Route::prefix('news')->name('news.')->group(function () {
        Route::prefix('{news}')->group(function () {
            Route::get('/likes', [NewsController::class, 'likes'])->name('likes');
            Route::get('/comments', [NewsController::class, 'comments'])->name('comments');
            Route::post('/likes', [NewsController::class, 'storeLike'])->name('store.likes');
            Route::post('/comments', [NewsController::class, 'storeComment'])->name('store.comments');
        });
    });

    Route::apiResource('comments', CommentController::class);
    Route::prefix('comments')->name('comments.')->group(function () {
        Route::prefix('{comment}')->group(function () {
            Route::get('/comments', [CommentController::class, 'comments'])->name('comments');
        });
    });
});
