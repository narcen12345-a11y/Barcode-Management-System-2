<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Resources\RoleResource;
use App\Services\RoleService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function __construct(
        private readonly RoleService $roleService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'trashed']);
        $perPage = $request->input('per_page', 10);

        $roles = $this->roleService->findAllPaginated($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => RoleResource::collection($roles),
            'meta' => [
                'current_page' => $roles->currentPage(),
                'last_page' => $roles->lastPage(),
                'per_page' => $roles->perPage(),
                'total' => $roles->total(),
            ],
        ]);
    }

    public function all(): JsonResponse
    {
        $roles = $this->roleService->findAll();

        return response()->json([
            'success' => true,
            'data' => RoleResource::collection($roles),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $role = $this->roleService->findById($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new RoleResource($role->load('permissions')),
        ]);
    }

    public function store(StoreRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dibuat.',
            'data' => new RoleResource($role),
        ], 201);
    }

    public function update(int $id, UpdateRoleRequest $request): JsonResponse
    {
        $role = $this->roleService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diperbarui.',
            'data' => new RoleResource($role),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->roleService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dihapus.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->roleService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dipulihkan.',
        ]);
    }
}
