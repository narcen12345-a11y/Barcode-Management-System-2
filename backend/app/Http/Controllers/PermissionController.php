<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;
use App\Http\Resources\PermissionResource;
use App\Services\PermissionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function __construct(
        private readonly PermissionService $permissionService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'module', 'is_active', 'trashed']);
        $perPage = $request->input('per_page', 10);

        $permissions = $this->permissionService->findAllPaginated($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions),
            'meta' => [
                'current_page' => $permissions->currentPage(),
                'last_page' => $permissions->lastPage(),
                'per_page' => $permissions->perPage(),
                'total' => $permissions->total(),
            ],
        ]);
    }

    public function all(): JsonResponse
    {
        $permissions = $this->permissionService->findAll();

        return response()->json([
            'success' => true,
            'data' => PermissionResource::collection($permissions),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $permission = $this->permissionService->findById($id);

        if (!$permission) {
            return response()->json([
                'success' => false,
                'message' => 'Permission tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new PermissionResource($permission),
        ]);
    }

    public function store(StorePermissionRequest $request): JsonResponse
    {
        $permission = $this->permissionService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dibuat.',
            'data' => new PermissionResource($permission),
        ], 201);
    }

    public function update(int $id, UpdatePermissionRequest $request): JsonResponse
    {
        $permission = $this->permissionService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil diperbarui.',
            'data' => new PermissionResource($permission),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->permissionService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dihapus.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->permissionService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Permission berhasil dipulihkan.',
        ]);
    }
}
