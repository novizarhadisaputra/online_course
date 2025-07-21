<?php

namespace App\Models;

use App\Models\Tag;
use App\Models\Like;
use App\Models\User;
use App\Models\View;
use App\Models\Price;
use App\Models\Score;
use App\Models\Bundle;
use App\Models\Coupon;
use App\Models\Lesson;
use App\Models\Review;
use App\Models\Comment;
use App\Models\Section;
use App\Models\Category;
use App\Models\Metadata;
use App\Models\Progress;
use App\Models\Taggable;
use App\Models\BundleItem;
use App\Models\Competence;
use App\Models\Couponable;
use App\Models\Enrollment;
use App\Traits\ModelTrait;
use App\Models\PaymentLink;
use App\Models\Transaction;
use App\Models\Announcement;
use App\Models\TransactionDetail;
use Spatie\MediaLibrary\HasMedia;
use App\Models\ModelHasCompetence;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
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


    protected $casts = [
        'meta' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tags(): MorphToMany
    {
        return $this->morphToMany(Tag::class, 'taggable', Taggable::class);
    }

    public function competences(): MorphToMany
    {
        return $this->morphToMany(Competence::class, 'model', ModelHasCompetence::class);
    }

    public function learningMethods(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function coupons(): MorphToMany
    {
        return $this->morphToMany(Coupon::class, 'model', Couponable::class);
    }

    public function bundles(): MorphToMany
    {
        return $this->morphToMany(Bundle::class, 'model', BundleItem::class);
    }

    public function viewers(): MorphMany
    {
        return $this->morphMany(View::class, 'model');
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function comments(): MorphMany
    {
        return $this->morphMany(Comment::class, 'model');
    }

    public function announcements(): MorphMany
    {
        return $this->morphMany(Announcement::class, 'model');
    }

    public function price(): MorphOne
    {
        return $this->morphOne(Price::class, 'priceable');
    }

    public function payment_link(): MorphOne
    {
        return $this->morphOne(PaymentLink::class, 'linkeable');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class, 'course_id', 'id');
    }

    public function lessons(): HasManyThrough
    {
        return $this->hasManyThrough(Lesson::class, Section::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function likes(): MorphToMany
    {
        return $this->morphToMany(User::class, 'likeable', Like::class);
    }

    public function transactions(): MorphToMany
    {
        return $this->morphToMany(Transaction::class, 'model', TransactionDetail::class)
            ->withPivot(['id', 'qty', 'units', 'price']);
    }

    public function students(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', Enrollment::class);
    }

    public function enrollments(): MorphMany
    {
        return $this->morphMany(Enrollment::class, 'model');
    }

    public function progress(): MorphOne
    {
        return $this->morphOne(Progress::class, 'model')->orderBy('created_at', 'desc');
    }

    public function score(): MorphOne
    {
        return $this->morphOne(Score::class, 'model')->latestOfMany();
    }

    public function metadata(): MorphOne
    {
        return $this->morphOne(Metadata::class, 'model');
    }
}
