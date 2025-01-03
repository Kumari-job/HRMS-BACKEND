<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeBank extends Model
{
    use LogsActivity;
    protected $table = 'employee_banks';

    protected $fillable = [
        'employee_id',
        'account_number',
        'account_name',
        'bank_name',
        'bank_branch',
        'is_primary',
        'created_by',
        'updated_by',
    ];

    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "An employee bank has been {$eventName}")
            ->logOnly(['employee.name','employee_id']);
    }
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }

    public function createdBy():BelongsTo
    {
        return $this->belongsTo(User::class,'created_by');
    }
    public function updatedBy():BelongsTo
    {
        return $this->belongsTo(User::class,'updated_by');
    }
}
