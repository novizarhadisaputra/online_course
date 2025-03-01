<?php

namespace App\Models;

use App\Models\Course;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasUuids;

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
