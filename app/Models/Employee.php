<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'name',
        'email',
        'image',
        'mobile',
        'address',
        'gender',
        'date_of_birth',
        'citizenship_number',
        'citizenship_front_image',
        'citizenship_back_image',
        'pan_number',
    ];
}
