<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidCredentialsException;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * @OA\Info(
 *      title="API Documentation",
 *      description="API Description for Simpsons App",
 *      version="1.0.0"
 * )
 *
 */
readonly class AuthController
{

    private AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * @OA\Post(
     *      path="/api/login",
     *      summary="Login user",
     *      tags={"Authentication"},
     *           @OA\RequestBody(
     *           required=true,
     *           description="The user credentials",
     *           @OA\JsonContent(ref="#/components/schemas/LoginData")
     *        ),
     *      @OA\Response(
     *          response=200,
     *          description="The user was sucessfully logged in",
     *          @OA\JsonContent(ref="#/components/schemas/Token")
     *       ),
     *     @OA\Response(
     *           response=401,
     *           description="The provided credentials are invalid"
     *        ),
     *     @OA\Response(
     *            response=400,
     *            description="The provided request body is invalid"
     *         )
     * )
     *
     * @OA\Schema(
     *      schema="LoginData",
     *      type="object",
     *      @OA\Property(property="username", type="string", example="test"),
     *      @OA\Property(property="password", type="string", example="password123")
     *  )
     *
     * @OA\Schema(
     *       schema="Token",
     *       type="object",
     *       @OA\Property(property="token", type="string", example="example_token")
     *   )
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
