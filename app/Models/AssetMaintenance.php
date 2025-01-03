<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class AssetMaintenance extends Model
{
    use LogsActivity;
    protected $table = 'asset_maintenances';

    protected $fillable = [
        'asset_id',
        'problem',
        'start_date',
        'end_date',
        'cost',
        'details',
    ];
    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "An asset maintenance has been {$eventName}")
            ->logOnly(['asset.title','asset.code','problem','cost','start_date','end_date']);
    }
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
