<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\User;
use App\Models\View;
use App\Models\Bundle;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Taggable;
use App\Models\BundleItem;
use App\Models\Enrollment;
use App\Traits\ModelTrait;
use App\Models\Certificate;
use App\Models\PaymentLink;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Course extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, ModelTrait;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Get the category that owns the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all of the tags for the courses.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', Taggable::class);
    }

    /**
     * Get all of the competences for the courses.
     */
    public function competences(): MorphToMany
    {
        return $this->morphToMany(Competence::class, 'model', ModelHasCompetence::class);
    }

    /**
     * Get all of the courses's learningMethods.
     */
    public function learningMethods(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get all of the coupons for the courses.
     */
    public function coupons(): MorphToMany
    {
        return $this->morphToMany(Coupon::class, 'model', Couponable::class);
    }

    /**
     * Get all of the bundles for the courses.
     */
    public function bundles(): MorphToMany
    {
        return $this->morphToMany(Bundle::class, 'model', BundleItem::class);
    }

    /**
     * Get all of the viewers for the course.
     */
    public function viewers(): MorphToMany
    {
        return $this->morphToMany(User::class, 'viewable', View::class);
    }

    /**
     * Get all of the courses's reviews.
     */
    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    /**
     * Get all of the courses's comments.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'model');
    }

    /**
     * Get all of the courses's announcements.
     */
    public function announcements(): MorphMany
    {
        return $this->morphMany(Announcement::class, 'model');
    }

    /**
     * Get all of the courses's price.
     */
    public function price(): MorphOne
    {
        return $this->morphOne(Price::class, 'priceable');
    }

    /**
     * Get the course's link.
     */
    public function payment_link(): MorphOne
    {
        return $this->morphOne(PaymentLink::class, 'linkeable');
    }

    /**
     * Get all of the sections for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'course_id', 'id');
    }

    /**
     * Get all of the lessons for the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    /**
     * Get the user that owns the Course
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all of the likes for the course.
     */
    public function likes(): MorphToMany
    {
        return $this->morphToMany(User::class, 'likeable', Like::class);
    }

    /**
     * Get all of the transactions for the course.
     */
    public function transactions(): MorphToMany
    {
        return $this->morphToMany(Transaction::class, 'model', TransactionDetail::class)
            ->withPivot(['id', 'qty', 'units', 'price']);
    }

    /**
     * Get all of the students for the course.
     */
    public function students(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', Enrollment::class);
    }

    /**
     * Get all of the course's enrollments.
     */
    public function enrollments(): MorphMany
    {
        return $this->morphMany(Enrollment::class, 'model');
    }
}
