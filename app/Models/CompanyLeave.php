<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([CompanyScope::class])]
class CompanyLeave extends Model
{
    protected $fillable = [
      'name',
      'days',
      'year',
      'gender',
      'marital_status',
      'exclude_holiday',
      'exclude_weekend',
      'icon_index'
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
