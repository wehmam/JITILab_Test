<?php

namespace App\Exceptions;

use App\Enums\Errors\CommonError;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\Exceptions\MissingAbilityException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Throwable $e
     * @return \Illuminate\Http\Response|JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $e)
    {
        if ($e instanceof AccessDeniedHttpException || $e instanceof MissingAbilityException) {
            $forbidden = CommonError::ERR_FORBIDDEN_ACCESS;

            return response()->json([
                'status' => false,
                'error' => $forbidden->value,
                'message' => $forbidden->message()
            ], $forbidden->httpStatusCode());
        }

        if ($e instanceof \Illuminate\Auth\AuthenticationException) {
            $error = CommonError::ERR_INVALID_ACCESS_TOKEN;

            return response()->json($error->toMap(), $error->httpStatusCode());
        }

        if ($e instanceof ValidationException) {
            $error = CommonError::ERR_BAD_REQUEST;

            return response()->json($error->toMap($e->getMessage()), $error->httpStatusCode());
        }

        if ($this->isHttpException($e)) {
            $error = CommonError::createFromHttpStatusCode($e->getStatusCode());

            if (filled($error)) {
                return \response()->json($error->toMap(), $error->httpStatusCode());
            }

            return \response()->json([
                'status' => false,
                'error' => 'HTTP_HANDLER',
                'messsage' => $e->getMessage()
            ], $e->getStatusCode());
        }

        return \response()->json([
            'status' => 500,
            "error" => CommonError::ERR_INTERNAL_ERROR->value,
            'message' => $e->getMessage()
        ], 500);
    }
}
