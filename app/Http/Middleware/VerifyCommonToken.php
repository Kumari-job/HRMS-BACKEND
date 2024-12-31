<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;


use Illuminate\Support\Facades\Config;

class VerifyCommonToken
{

    // Verify token to allow data exchange between applications i.e idP, crm,

    public function handle(Request $request, Closure $next)
    {

        $token = $this->getCredentials($request);


        if (!$this->isValidCredential($token)) {
            return response()->json(['error' => 'Unauthorized to common Token'], 401);
        }

        return $next($request);
    }

    private function getCredentials(Request $request)
    {
        $authorization = $request->header('Authorization');
        $token = str_replace('Bearer ', '', $authorization);
        return $token;
    }

    private function isValidCredential($token): bool
    {
        if (!empty($token)) {
            $expectedToken = Config::get('custom.common_token');
            return $token === $expectedToken;
        } else {
            return false;
        }
    }
}

