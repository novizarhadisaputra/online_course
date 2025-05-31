<?php

namespace App\Models;

use App\Models\User;
use App\Models\Stock;
use App\Models\Product;
use App\Models\BranchUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Branch extends Model
{
    use HasUuids;

    protected $guarded = [];

    /**
     * The users that belong to the Branch
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, BranchUser::class, 'branch_id', 'user_id');
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
