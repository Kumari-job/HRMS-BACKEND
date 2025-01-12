<?php

namespace App\Models;

use App\Models\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Model;
#[ScopedBy([CompanyScope::class])]
class PayrollSetting extends Model
{
    protected $fillable = [
        'company_id',
        'cit_number',
        'pf_number',
        'ssf_number',
        'bank_name',
        'bank_branch_name',
        'bank_account_name',
        'bank_account_number',
        'created_by',
        'updated_by',
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
