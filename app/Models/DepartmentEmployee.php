<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class DepartmentEmployee extends Model
{
    use LogsActivity;
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

    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "A department employee has been {$eventName}")
            ->logOnly(['department.name','employee.name','designation']);
    }
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
