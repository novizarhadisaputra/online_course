<?php

namespace App\Models;

use App\Models\User;
use App\Models\Course;
use App\Models\Review;
use App\Models\Address;
use App\Models\PaymentMethod;
use App\Models\TransactionLog;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Transaction extends Model
{
    use HasUuids;

    protected $casts = [
        'data' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'id');
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TransactionLog::class, 'transaction_id', 'id');
    }

    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', TransactionDetail::class)
            ->withPivot(['id', 'qty', 'units', 'price']);
    }

    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, TransactionDetail::class);
    }

    public function payment_method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }

    public function accounts(): BelongsToMany
    {
        return $this->belongsToMany(UserWallet::class, TransactionAccount::class, 'transaction_id', 'user_wallet_id');
    }
}
