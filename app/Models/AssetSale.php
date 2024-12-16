<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetSale extends Model
{
    protected $table = 'asset_sales';

    protected $fillable = [
        'asset_id',
        'price',
        'details',
        'sold_to',
        'sold_by'
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class,'asset_id');
    }
}
