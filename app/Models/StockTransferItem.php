<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use HasUuids;

    /**
     * Get the stockTransfer that owns the StockTransferItem
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function stockTransfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class);
    }
}
