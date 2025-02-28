<?php

use App\Http\Middleware\ApiLogger;
use App\Http\Middleware\ApiAuthBase;
use Illuminate\Foundation\Application;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'apiLogger' => ApiLogger::class,
            'apiAuthBase' => ApiAuthBase::class
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {

        $exceptions->render(function (ThrottleRequestsException $e, $request) {
            $error =  __('messages.too_many_attempts');
            $code = 429;
            if ($request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $error,
                    'code' => $code
                ], $code);
            } else {
                abort($code);
            }
        });

        $exceptions->render(function (ValidationException $e, $request) {
            
            if($request->is('api/*')) {

                $error_code = 422;

                $errors = $e->errors();

                return response()->json([
                    'success' => false,
                    'message' => $errors[array_key_first($errors)][0],
                    'code' => $error_code
                ]);
            }
        });

        $exceptions->render(function(AccessDeniedHttpException $e, $request) {

            if($request->is('api/*')) {

                $error_code = 403;

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => $error_code
                ], $error_code);
            }
        });


        $exceptions->render(function (AuthenticationException $e, $request) {

            if ($request->is('api/*')) { 

                return response()->json([
                    'success' => false,
                    'message' => $e->getMessage(),
                    'code' => 401
                ], 200); 
            }
        });

    })->create();
