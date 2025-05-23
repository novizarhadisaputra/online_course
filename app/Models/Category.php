<?php

namespace App\Models;

use App\Models\Course;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\MediaLibrary\InteractsWithMedia;

class Category extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $guarded = [];

   /**
    * Get all of the courses for the Category
    *
    * @return \Illuminate\Database\Eloquent\Relations\HasMany
    */
   public function courses(): HasMany
   {
       return $this->hasMany(Course::class);
   }
}
