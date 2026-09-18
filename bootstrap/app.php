<?php

use App\Domain\Datasets\DatasetValidationException;
use App\Domain\Learning\Content\ContentValidationException;
use App\Domain\Learning\Content\InvalidLearningComponentException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Log;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $exception): void {
            if ($exception instanceof ContentValidationException
                || $exception instanceof DatasetValidationException
                || $exception instanceof InvalidLearningComponentException
            ) {
                Log::warning('BelajarData repository boundary error.', [
                    'exception' => $exception::class,
                    'message' => $exception->getMessage(),
                ]);
            }
        });
    })->create();
