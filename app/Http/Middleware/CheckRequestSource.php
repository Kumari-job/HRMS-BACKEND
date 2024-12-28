<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRequestSource
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        if (auth()->check()) {
            $request->attributes->set('request_source', 'user');
        } elseif (auth()->user() === null && auth()->guard('api')->check()) {
            $request->attributes->set('request_source', 'client');
        } else {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
