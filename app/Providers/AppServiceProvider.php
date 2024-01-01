<?php

namespace App\Providers;

use App\Enums\Errors\CommonError;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Response::macro('jsonFailed', function (
            string $error,
            string|null $message = null,
            bool $status = false,
            int $statusCode = 400
        ) {

            $error = CommonError::tryFrom($error);

            if (filled($error)) {
                return Response::json(
                    data: $error->toMap(),
                    status: $error->httpStatusCode()
                );
            }

            return Response::json([
                'status' => false,
                'error' => 'EXCEPTION_ERROR',
                'message' => $message ?? $error
            ], $statusCode);
        });
    }
}
