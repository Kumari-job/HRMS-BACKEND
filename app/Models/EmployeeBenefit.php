<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmployeeBenefit extends Model
{
    protected $table = 'employee_benefits';

    protected $fillable = [
      'employee_id',
      'pan',
      'ssf',
      'cit',
      'pf',
      'created_by',
      'updated_by',
    ];

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
