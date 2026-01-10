<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\AuthResource;
use App\Services\AuthService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    protected AuthService $authService;

    public function __construct(AuthService $authService)
    {
        $this->authService = $authService;
    }

    /**
     * LOGIN
     *
     * Process:
     * 1. Validate credentials
     * 2. Authenticate user via AuthService
     * 3. Return token and user info
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $result = $this->authService->login(
            $request->validated()['email'],
            $request->validated()['password']
        );

        return (new AuthResource($result))->response();
    }

    /**
     * LOGOUT
     *
     * Process:
     * 1. Delete current access token
     * 2. User must login again
     */
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully',
        ]);
    }

    /**
     * GET CURRENT USER
     *
     * Return authenticated user info
     */
    public function me(Request $request): JsonResponse
    {
        $userData = $this->authService->getUserDetails($request->user());

        return (new AuthResource(['user' => $request->user(), 'message' => 'User retrieved successfully']))
            ->response();
    }
}
