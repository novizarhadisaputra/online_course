<?php

namespace App\Models;

use App\Observers\TagObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

#[ObservedBy([TagObserver::class])]
class Tag extends Model
{
    use HasUuids;

    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'taggable', Taggable::class);
    }

    public function events(): MorphToMany
    {
        return $this->morphedByMany(Event::class, 'taggable', Taggable::class);
    }

    public function news(): MorphToMany
    {
        return $this->morphedByMany(News::class, 'taggable', Taggable::class);
    }
}
