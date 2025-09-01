<?php

namespace App\Models;

use App\Models\User;
use App\Models\Price;
use App\Models\Course;
use App\Models\Metadata;
use App\Models\PrivateClassItem;
use App\Models\Enrollment;
use App\Traits\ModelTrait;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class PrivateClass extends Model implements HasMedia
{
    use ModelTrait, HasUuids, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'short_description',
        'description',
        'duration',
        'duration_units',
        'max_participants',
        'start_date',
        'end_date',
        'status',
        'is_paid'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => 'boolean',
        'is_paid' => 'boolean',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(PrivateClassItem::class, 'private_class_id', 'id');
    }

    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', PrivateClassItem::class);
    }

    public function price(): MorphOne
    {
        return $this->morphOne(Price::class, 'priceable');
    }

    public function metadata(): MorphOne
    {
        return $this->morphOne(Metadata::class, 'model');
    }

    public function enrollments(): MorphToMany
    {
        return $this->morphToMany(User::class, 'model', Enrollment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_paid', true);
    }

    public function scopeFree($query)
    {
        return $query->where('is_paid', false);
    }

    public function getRouteKeyName()
    {
        return 'slug';
    }
}