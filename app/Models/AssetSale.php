<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AssetSale extends Model
{
    use LogsActivity;
    protected $table = 'asset_sales';

    protected $fillable = [
        'asset_id',
        'price',
        'details',
        'sold_to',
        'sold_by'
    ];
    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "An asset sale has been {$eventName}")
            ->logOnly(['asset.title','asset.code','price']);
    }
    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class,'asset_id');
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'sold_by');
    }
    public function scopeForCompany(Builder $query)
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
