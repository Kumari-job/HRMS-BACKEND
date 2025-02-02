<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PermissionCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $company_id = Auth::user()->selectedCompany->company_id;
        $role = Auth::user()->roles->where('company_id', $company_id)->first();
        if(!$role){
            return response()->json(['error' => true, 'message' => 'Role not assigned'], 403);
        }

        // bypass if role is super-admin
        if($role->name == "super-admin"){
            return $next($request);
        }

        $userPermissions = $role->permissions->pluck('name')->toArray();

        if ($permission && !in_array($permission, $userPermissions)) {
            return response()->json(['error' => true, 'message' => 'You are not authorized'], 403);
        } else {
            return $next($request);
        }

    }
}
