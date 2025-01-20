<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
}
