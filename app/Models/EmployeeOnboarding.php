<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeOnboarding extends Model
{
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

    public function employee()
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
