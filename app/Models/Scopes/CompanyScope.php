<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        $request = request();
        $requestSource = $request->attributes->get('request_source');
        $company_id = ($request->company_id && $requestSource === 'client') 
            ? $request->company_id 
            : Auth::user()->selectedCompany->company_id;
            
        $builder->where('company_id', $company_id);
    }
}
