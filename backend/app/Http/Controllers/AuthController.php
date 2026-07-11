<?php

namespace App\Http\Controllers;

use App\DTOs\ChangePasswordDTO;
use App\DTOs\LoginRequestDTO;
use App\Http\Requests\ChangePasswordRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Resources\UserResource;
use App\Services\AuthenticationService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthenticationService $authenticationService,
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        $dto = LoginRequestDTO::fromRequest($request->validated());

        $result = $this->authenticationService->login($dto);

        return response()->json([
            'success' => true,
            'message' => 'Login berhasil.',
            'data' => [
                'user' => new UserResource($result['user']),
                'token' => $result['token'],
            ],
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        $this->authenticationService->logout($request->user());

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil.',
        ]);
    }

    public function me(Request $request): JsonResponse
    {
        $user = $this->authenticationService->getCurrentUser($request->user());

        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function changePassword(ChangePasswordRequest $request): JsonResponse
    {
        $dto = ChangePasswordDTO::fromRequest($request->validated());

        $this->authenticationService->changePassword($dto);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.',
        ]);
    }
}
