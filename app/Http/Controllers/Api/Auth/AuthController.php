<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\Auth\LoginResource;
use App\Http\Service\Auth\AuthService;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    )
    {
    }

    public function login(LoginRequest $request): JsonResponse
    {

        $login = $this->authService->login(
            credentials: $request->validated(),
        );

        return ApiResponse::success(
            message: 'Login berhasil',
            data: LoginResource::make($login),
        );
    }
}
