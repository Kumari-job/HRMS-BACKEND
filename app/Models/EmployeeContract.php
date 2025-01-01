<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EmployeeContract extends Model
{
    public $table = 'employee_contracts';
    public $fillable = [
        'employee_id',
        'contract_type',
        'probation_end_at',
        'job_description',
        'gross_salary',
        'basic_salary',
        'pf_from_employee',
        'pf_from_company',
        'gratuity',
        'cit_percentage',
        'cit_amount',
        'ssf_amount',
        'created_by',
        'updated_by',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function scopeForCompany(Builder $query)
    {
        if (Auth::check()) {
            $company_id = request('company_id') ?? Auth::user()->selectedCompany->company_id;
            $query->whereHas('employee', function ($query) use ($company_id) {
                $query->where('company_id', $company_id);
            });
        }
        return $query;
    }
}
