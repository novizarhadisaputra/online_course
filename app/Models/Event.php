<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\News;
use App\Models\View;
use App\Models\Price;
use App\Models\Course;
use App\Models\Review;
use App\Models\Comment;
use App\Models\Category;
use App\Models\Taggable;
use App\Models\Enrollment;
use App\Traits\ModelTrait;
use App\Models\Appointment;
use App\Models\PaymentLink;
use App\Models\Transaction;
use App\Models\ModelHasEvent;
use App\Models\TransactionDetail;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Event extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, ModelTrait;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Get the category that owns the Event
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all of the tags for the post.
     */
    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', Taggable::class);
    }

    /**
     * Get all of the viewers for the course.
     */
    public function viewers(): MorphMany
    {
        return $this->morphMany(View::class, 'model');
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
     * Get all of the events's price.
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
     * Get all of the transactions for the event.
     */
    public function transactions(): MorphToMany
    {
        return $this->morphToMany(Transaction::class, 'model', TransactionDetail::class)
            ->withPivot(['id', 'qty', 'units', 'price']);
    }

    /**
     * Get all of the events's appointments.
     */
    public function appointments(): MorphMany
    {
        return $this->morphMany(Appointment::class, 'model');
    }

    /**
     * Get all of the course's enrollments.
     */
    public function enrollments(): MorphMany
    {
        return $this->morphMany(Enrollment::class, 'model');
    }

    /**
     * Get all of the courses that are assigned this tag.
     */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', ModelHasEvent::class);
    }

    /**
     * Get all of the news that are assigned this tag.
     */
    public function news(): MorphToMany
    {
        return $this->morphedByMany(News::class, 'model', ModelHasEvent::class);
    }
}
