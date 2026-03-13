<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Auth\AuthenticationException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(fn() => null);
    })
    ->withExceptions(function (Exceptions $exceptions): void {

        $exceptions->render(function (AuthenticationException $e, Request $request) {
            return response()->json(['message' => 'No autenticado. Token requerido.'], 401);
        });

        $exceptions->render(function (TokenExpiredException $e, Request $request) {
            return response()->json(['message' => 'Token expirado.'], 401);
        });

        $exceptions->render(function (TokenInvalidException $e, Request $request) {
            return response()->json(['message' => 'Token inválido.'], 401);
        });

        $exceptions->render(function (JWTException $e, Request $request) {
            return response()->json(['message' => 'Token ausente o malformado.'], 401);
        });

    })->create();