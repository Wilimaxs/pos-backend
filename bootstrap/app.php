<?php

use App\Support\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request) => $request->is('api/*') || $request->expectsJson(),
        );

        $exceptions->render(
            function (
                ValidationException $exception,
                Request             $request
            ) {
                if (!$request->is('api/*')) {
                    return null;
                }

                return ApiResponse::error(
                    message: 'Data yang diberikan tidak valid',
                    data: [
                        'errors' => $exception->errors(),
                    ],
                    statusCode: 422,
                );
            }
        );

        $exceptions->render(
            function (
                NotFoundHttpException $exception,
                Request               $request
            ) {
                if (!$request->is('api/*')) {
                    return null;
                }

                $previous = $exception->getPrevious();

                $message = $previous instanceof ModelNotFoundException
                    ? class_basename($previous->getModel()) . ' tidak ditemukan'
                    : 'Endpoint tidak ditemukan';

                return ApiResponse::error(
                    message: $message,
                    statusCode: 404,
                );
            }
        );

        $exceptions->render(
            function (
                AuthenticationException $exception,
                Request                 $request,
            ) {
                if (!$request->is('api/*')) {
                    return null;
                }

                return ApiResponse::error(
                    message: 'Autentikasi diperlukan',
                    statusCode: 401,
                );
            }
        );

        $exceptions->render(
            function (
                HttpExceptionInterface $exception,
                Request                $request,
            ) {
                if (!$request->is('api/*')) {
                    return null;
                }

                $statusCode = $exception->getStatusCode();

                $message = match ($statusCode) {
                    403 => 'Anda tidak memiliki izin untuk melakukan tindakan ini',
                    405 => 'Metode HTTP tidak diizinkan',
                    419 => 'Sesi telah kedaluwarsa',
                    429 => 'Terlalu banyak permintaan',
                    default => 'Permintaan tidak dapat diproses',
                };

                return ApiResponse::error(
                    message: $message,
                    statusCode: $statusCode,
                );
            }
        );

        $exceptions->render(
            function (
                Throwable $exception,
                Request   $request,
            ) {
                if (!$request->is('api/*')) {
                    return null;
                }

                return ApiResponse::error(
                    message: app()->environment('local')
                        ? $exception->getMessage()
                        : 'Terjadi kesalahan pada server',
                    statusCode: 500,
                );
            }
        );
    })->create();
