<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
}
