<?php

namespace App\Models;

use App\Traits\ModelTrait;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\MediaLibrary\HasMedia;

class ProductCategory extends Model implements HasMedia
{
    use HasUuids, InteractsWithMedia, ModelTrait;

    protected $guarded = [];
}
