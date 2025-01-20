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
        // if (auth()->guard()->check()) {
        //     $request->attributes->set('request_source', 'user');
        //     return $next($request);
        // } elseif (auth()->user() === null && auth()->guard('api')->check()) {
        //     $request->attributes->set('request_source', 'client');
        //     return $next($request);
        // } else {
        //     return response()->json(['message' => 'Unauthorized client'], 401);
        // }

        if (auth()->guard()->check()) {
            $request->attributes->set('request_source', 'user');
            return $next($request);
        } else {
            $request->attributes->set('request_source', 'client');
            return $next($request);
        }
    }
}
