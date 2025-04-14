<?php

namespace App\Models;

use App\Models\User;
use App\Models\Course;
use App\Models\Review;
use App\Models\PaymentMethod;
use App\Models\TransactionLog;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class Transaction extends Model
{
    use HasUuids;

    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
    ];

    /**
     * Get the user that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Get all of the details for the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details(): HasMany
    {
        return $this->hasMany(TransactionDetail::class, 'transaction_id', 'id');
    }

    /**
     * Get all of the logs for the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function logs(): HasMany
    {
        return $this->hasMany(TransactionLog::class, 'transaction_id', 'id');
    }

    /**
     * Get all of the courses that are assigned this tag.
     */
    public function courses(): MorphToMany
    {
        return $this->morphedByMany(Course::class, 'model', TransactionDetail::class)
            ->withPivot(['id', 'qty', 'units', 'price']);
    }

    /**
     * Get all of the reviews for the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasManyThrough
     */
    public function reviews(): HasManyThrough
    {
        return $this->hasManyThrough(Review::class, TransactionDetail::class);
    }

    /**
     * Get the payment_channel that owns the Transaction
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function payment_method(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
}
