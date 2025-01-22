<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeePasswordIsChanged
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->is_password_changed == 0) {
            return response()->json([
                'error' => true,
                'message' => 'Please change your password.',
                // 'url' => url(config('custom.client_app.hrms_app_frontend_url').'/auth/change-password')
            ], 412);
        }

        return $next($request);
    }
}
