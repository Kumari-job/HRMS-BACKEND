<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([CompanyScope::class])]
class CompanyProfile extends Model
{
    protected $fillable = [
      'company_id',
      'fiscal_calendar_type',
      'fiscal_start_month',
      'week_start_day',
      'week_end_day',
      'weekly_leaves',
      'country',
    ];
}
