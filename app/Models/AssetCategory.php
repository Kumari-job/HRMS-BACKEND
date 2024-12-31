<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetCategory extends Model
{
    protected $table = 'asset_categories';
    protected $fillable = [
        'title',
        'description',
    ];

    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy():BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
