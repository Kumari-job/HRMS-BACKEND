<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Department extends Model
{
    use SoftDeletes, LogsActivity;
    protected $guarded = [];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "A department has been {$eventName}")
            ->logOnly(['branch.name', 'name']);
    }
    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function headOfDepartment(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'department_employees', 'department_id', 'employee_id')
            ->withPivot(['designation', 'joined_at', 'created_at']);
    }


    // scopes 

    public function scopeForCompany(Builder $query)
    {
        $company_id = request('company_id') ?? Auth::user()->selectedCompany->company_id;
        $query->whereHas('branch', function ($query) use ($company_id) {
            $query->where('company_id', $company_id);
        });
        return $query;
    }
}
