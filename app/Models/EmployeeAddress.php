<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeAddress extends Model
{
    protected $table = 'employee_addresses';

    protected $fillable = [
        'employee_id',
        'p_country',
        'p_district',
        'p_vdc_or_municipality',
        'p_ward',
        'p_state',
        'p_street',
        'p_house_number',
        'p_zip_code',
        't_country',
        't_district',
        't_vdc_or_municipality',
        't_ward',
        't_state',
        't_street',
        't_house_number',
        't_zip_code',
    ];

    public function employee():BelongsTo
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
