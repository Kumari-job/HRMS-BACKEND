<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeExperience extends Model
{
    protected $table = 'employee_experiences';

    protected $fillable = [
        'employee_id',
        'designation',
        'industry',
        'job_level',
        'company',
        'experience_letter',
        'from_date',
        'to_date',
        'created_by',
        'updated_by',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class,'employee_id');
    }
}
