<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class AssetUsage extends Model
{
    protected $table = 'asset_usages';

    protected $fillable = [
        'asset_id',
        'employee_id',
        'assigned_at',
        'assigned_end_at',
        'assigned_by'
    ];

    public function employee():BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function asset():BelongsTo
    {
        return $this->belongsTo(Asset::class,'asset_id');
    }

    public function assignedBy():BelongsTo
    {
        return $this->belongsTo(User::class,'assigned_by');
    }

    public function scopeForCompany($query)
    {
        if (Auth::check()) {
            $company_id = Auth::user()->selectedCompany->company_id;
            $query->whereHas('employee', function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }
        return $query;
    }
}
