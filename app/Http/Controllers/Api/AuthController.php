<?php

namespace App\Http\Controllers\Api;

use App\Enums\Errors\CommonError;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Services\AuthServices;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Constructs a new instance of the class.
     *
     * @param SessionService $authServices The session service to use. Defaults to a new instance of AuthServices.
     */
    public function __construct(private readonly AuthServices $authService = new AuthServices())
    {
    }

    public function login(LoginRequest $request) {
        try {
            $data = $this->authService->login(
                data: $request->validated()
            );
        } catch (\Exception $exception) {
            Log::error('login session failed', [
                'message' => $exception->getMessage(),
                'trace' => $exception->getTrace(),
            ]);
            $error = CommonError::ERR_INVALID_CREDS;

            return response()->json($error->toMap(), $error->httpStatusCode());
        }

        return response()->json([
            'status' => true,
            'data' => $data,
        ]);
    }

    public function logout(Request $request) {
        $request->user()->tokens()->delete();
        return response()->success('Logout Berhasil', [], 200);
    }
}
