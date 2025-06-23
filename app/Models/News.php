<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Comment;
use App\Traits\ModelTrait;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class News extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, ModelTrait;

    protected $guarded = [];

    protected $casts = [
        'meta' => 'array',
    ];

    /**
     * Get the category that owns the News
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
     * Get all of the news's price.
     */
    public function price(): MorphOne
    {
        return $this->morphOne(Price::class, 'priceable');
    }

    /**
     * Get all of the viewers for the news.
     */
    public function viewers(): MorphMany
    {
        return $this->morphMany(View::class, 'model');
    }

    /**
     * Get all of the likes for the news.
     */
    public function likes(): MorphToMany
    {
        return $this->morphToMany(User::class, 'likeable', Like::class);
    }

    /**
     * Get all of the news's comments.
     */
    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'model');
    }
}
