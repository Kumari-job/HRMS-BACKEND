<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AssetDispose extends Model
{
    use LogsActivity;
    protected $table = 'asset_dispose';

    protected $fillable = [
        'asset_id',
        'details',
        'disposed_at',
        'disposed_by',
    ];

    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "An asset dispose has been {$eventName}")
            ->logOnly(['details','asset.title','asset.code','disposed_at']);
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

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class ,'asset_id');
    }

    public function disposedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'disposed_by');
    }
}
