<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyHoliday extends Model
{
    protected $fillable = [
        'date',
        'holiday',
        'description',
        'female_only',
        'holiday_for_religion',
    ];
}
