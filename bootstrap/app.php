<?php

use App\Http\Middleware\CheckRequestSource;
use App\Http\Middleware\VerifyCommonToken;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Routing\Router;
use Laravel\Passport\Http\Middleware\CheckClientCredentials;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        function (Router $router) {
            $router->middleware('api')
                ->prefix('api')
                ->group(base_path('routes/api.php'));

            $router->middleware(['client','check.source'])
                    ->prefix('api/client')
                    ->group(base_path('routes/client-api.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'verify_common_token' => VerifyCommonToken::class,
            'client' => CheckClientCredentials::class,
            'check.source' => CheckRequestSource::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
