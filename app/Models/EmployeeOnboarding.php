<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class EmployeeOnboarding extends Model
{
    use LogsActivity;
    protected $table = 'employee_onboardings';

    protected $fillable = [
        'employee_id',
        'shortlisted_at',
        'interviewed_at',
        'offered_at',
        'offer_letter',
        'offered_by',
        'joined_at',
        'employment_type',
        'created_by',
        'updated_by',
    ];
    public function getActivitylogOptions():LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "An employee address has been {$eventName}")
            ->logOnly(['employee.name','employee_id']);
    }

    public function offeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'offered_by');
    }

    public function employee()
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
