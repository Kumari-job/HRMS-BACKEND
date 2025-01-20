<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SelectedCompany extends Model
{
    protected $fillable = [
        'company_id',
        'user_id',
    ];
}
