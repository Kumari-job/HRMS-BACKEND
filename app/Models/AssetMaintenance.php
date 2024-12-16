<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AssetMaintenance extends Model
{
    protected $table = 'asset_maintenances';

    protected $fillable = [
        'asset_id',
        'problem',
        'start_date',
        'end_date',
        'cost',
        'details',
    ];
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class,'asset_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }

    public function scopeForCompany($query)
    {
        if (Auth::check()) {
            $company_id = Auth::user()->selectedCompany->company_id;
            $query->whereHas('asset.assetCategory', function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }
        return $query;
    }

}
