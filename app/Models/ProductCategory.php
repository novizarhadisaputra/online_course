<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\HasMedia;

class ProductCategory extends Model implements HasMedia
{
    protected $table = 'product_categories';

    use HasUuids, InteractsWithMedia;

    protected $guarded = [];
}
