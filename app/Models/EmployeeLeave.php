<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class EmployeeLeave extends Model
{
    protected $fillable = [
      'leave_id',
      'leave_type',
      'requested_by',
      'reason',
      'start_date',
      'end_date',
      'start_time',
      'end_time',
    ];

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }
    public function leave(): BelongsTo
    {
        return $this->belongsTo(CompanyLeave::class, 'leave_id');
    }
    public function leaveStatus(): HasMany
    {
        return $this->hasMany(EmployeeLeaveStatus::class, 'employee_leave_id');
    }

    public function scopeForCompany(Builder $query)
    {
        $company_id = request('company_id') ?? Auth::user()->selectedCompany->company_id;
        $query->whereHas('leave', function ($query) use ($company_id) {
            $query->where('company_id', $company_id);
        });
        return $query;
    }
}
