<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetDispose extends Model
{
    protected $table = 'asset_dispose';

    protected $fillable = [
        'asset_id',
        'details',
        'disposed_at',
        'disposed_by',
    ];

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class ,'asset_id');
    }

    public function disposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'disposed_by');
    }
}
