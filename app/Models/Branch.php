<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

#[ScopedBy([CompanyScope::class])]
class Branch extends Model
{
    use SoftDeletes, LogsActivity;

    protected $table = 'branches';

    protected $fillable = [
        'name',
        'email',
        'company_id',
        'location',
        'employee_id',
        'contact_number',
        'established_date',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->setDescriptionForEvent(fn(string $eventName) => "A branch has been {$eventName}")
            ->logOnly(['name', 'company_id', 'location']);
    }

    public function departments(): HasMany
    {
        return $this->hasMany(Department::class, 'branch_id');
    }
    public function manager(): BelongsTo
    {
        return $this->belongsTo(Employee::class, 'employee_id');
    }

}
