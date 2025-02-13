<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Cart;
use App\Models\Comment;
use App\Models\PaymentLink;
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
    use HasUuids, InteractsWithMedia;

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
        return $this->belongsTo(Category::class, 'category_id', 'id');
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
        return $this->morphMany(Comment::class, 'commentable');
    }

    /**
     * Get all of the courses's prices.
     */
    public function prices(): MorphMany
    {
        return $this->morphMany(Price::class, 'priceable');
    }

    /**
     * Get the course's link.
     */
    public function payment_link(): MorphOne
    {
        return $this->morphOne(PaymentLink::class, 'linkeable');
    }

     /**
     * Get all of the courses's carts.
     */
    public function carts(): MorphMany
    {
        return $this->morphMany(Cart::class, 'cartable');
    }
}
