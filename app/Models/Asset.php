<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asset extends Model
{
    public $table = 'assets';

    public $fillable = [
      'asset_category_id',
      'vendor_id',
      'code',
      'title',
      'description',
      'brand',
      'cost',
      'model',
      'serial_number',
      'purchased_at',
      'warranty_end_at',
        'warranty_image',
        'guarantee_end_at',
        'guarantee_image',
        'status',
    ];

    public function assetCategory():BelongsTo
    {
        return $this->belongsTo(AssetCategory::class,'asset_category_id');
    }
    public function vendor():BelongsTo
    {
        return $this->belongsTo(Vendor::class,'vendor_id');
    }
}
