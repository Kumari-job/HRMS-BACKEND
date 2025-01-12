<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
#[ScopedBy([CompanyScope::class])]

class CompanyHoliday extends Model
{
    protected $fillable = [
        'date',
        'holiday',
        'description',
        'female_only',
        'holiday_for_religion',
    ];

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
