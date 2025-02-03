<?php

namespace App\Models;

use App\Models\Scopes\EmployeeScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class Attendance extends Model
{

    public $fillable = [
      'employee_id',
      'date',
      'is_present',
      'status',
      'created_by',
      'punch_in_at',
      'punch_out_at',
      'punch_in_ip',
      'punch_out_ip',
      'late_punch_in',
      'is_approved'
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class,'employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

}
