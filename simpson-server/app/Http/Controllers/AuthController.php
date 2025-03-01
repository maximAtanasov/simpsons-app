<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCredentialsException;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

readonly class AuthController
{

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * Handle user login and return a token.
     */
    public function login(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => 'Invalid input'], 400);
        }

        try {
            $token = $this->authService->login($request->username, $request->password);
        } catch (InvalidCredentialsException $e) {
            return response()->json(['error' => $e->getMessage()], 401);
        }

        return response()->json(['token' => $token]);
    }
}
