<?php

namespace App\Http\Controllers;

use App\DTOs\RegisterUserDTO;
use App\DTOs\VerifyUserDTO;
use App\Http\Requests\CreateUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\VerifyUserRequest;
use App\Http\Resources\UserResource;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly UserService $userService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'status', 'is_active', 'role', 'trashed']);
        $perPage = $request->input('per_page', 10);

        $users = $this->userService->findAllPaginated($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
            'meta' => [
                'current_page' => $users->currentPage(),
                'last_page' => $users->lastPage(),
                'per_page' => $users->perPage(),
                'total' => $users->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $user = $this->userService->findById($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new UserResource($user->load('roles')),
        ]);
    }

    public function store(CreateUserRequest $request): JsonResponse
    {
        $dto = RegisterUserDTO::fromRequest($request->validated());

        $user = $this->userService->create($dto);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dibuat.',
            'data' => new UserResource($user),
        ], 201);
    }

    public function update(int $id, UpdateUserRequest $request): JsonResponse
    {
        $user = $this->userService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diperbarui.',
            'data' => new UserResource($user),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->userService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dihapus.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->userService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dipulihkan.',
        ]);
    }

    public function verify(int $id, VerifyUserRequest $request): JsonResponse
    {
        $dto = new VerifyUserDTO(
            userId: $id,
            status: $request->validated()['status'],
        );

        $user = $this->userService->verify($dto);

        return response()->json([
            'success' => true,
            'message' => 'Status user berhasil diperbarui.',
            'data' => new UserResource($user),
        ]);
    }

    public function activate(int $id): JsonResponse
    {
        $user = $this->userService->activate($id);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil diaktifkan.',
            'data' => new UserResource($user),
        ]);
    }

    public function deactivate(int $id): JsonResponse
    {
        $user = $this->userService->deactivate($id);

        return response()->json([
            'success' => true,
            'message' => 'User berhasil dinonaktifkan.',
            'data' => new UserResource($user),
        ]);
    }

    public function resetPassword(int $id): JsonResponse
    {
        $newPassword = $this->userService->resetPassword($id);

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil di-reset.',
            'data' => [
                'new_password' => $newPassword,
            ],
        ]);
    }
}
