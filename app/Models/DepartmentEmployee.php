<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DepartmentEmployee extends Model
{
    protected $table = 'department_employees';

    protected $fillable = [
      'department_id',
      'employee_id',
      'designation',
      'telephone',
      'email',
      'mobile',
      'joined_at',
      'created_by'
    ];

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class,'department_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
