<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeLeaveStatus extends Model
{
    protected $fillable =[
        'employee_leave_id',
        'requested_to',
        'status'
    ];

    public function employeeLeave():BelongsTo
    {
        return $this->belongsTo(EmployeeLeave::class,'employee_leave_id');
    }

    public function requestedTo():BelongsTo
    {
        return $this->belongsTo(User::class,'requested_to');
    }
}
