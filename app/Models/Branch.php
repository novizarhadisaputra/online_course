<?php

namespace App\Models;

use App\Models\User;
use App\Models\Stock;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Filament\Models\Contracts\HasAvatar;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model implements HasAvatar, HasMedia
{
    use HasUuids, InteractsWithMedia;

    protected $guarded = [];

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->hasMedia('images') ? $this->getMedia('images')->first()->getTemporaryUrl(Carbon::now()->addHour()) : null;
    }

    /**
     * Get all of the users that are assigned this branches.
     */
    public function users(): MorphToMany
    {
        return $this->morphedByMany(User::class, 'model', ModelHasBranch::class);
    }

    /**
     * Get all of the stocks for the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stocks(): HasMany
    {
        return $this->hasMany(Stock::class);
    }

    /**
     * The products that belong to the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, Stock::class);
    }

    /**
     * Get all of the movements for the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function stock_movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
