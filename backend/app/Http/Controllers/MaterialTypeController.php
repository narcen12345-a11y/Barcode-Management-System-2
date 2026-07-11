<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialTypeRequest;
use App\Http\Requests\UpdateMaterialTypeRequest;
use App\Http\Resources\MaterialTypeResource;
use App\Services\MaterialTypeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialTypeController extends Controller
{
    public function __construct(
        private readonly MaterialTypeService $materialTypeService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'is_active', 'trashed']);
        $perPage = $request->input('per_page', 10);

        $materialTypes = $this->materialTypeService->findAllPaginated($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => MaterialTypeResource::collection($materialTypes),
            'meta' => [
                'current_page' => $materialTypes->currentPage(),
                'last_page' => $materialTypes->lastPage(),
                'per_page' => $materialTypes->perPage(),
                'total' => $materialTypes->total(),
            ],
        ]);
    }

    public function all(): JsonResponse
    {
        $materialTypes = $this->materialTypeService->findAll();

        return response()->json([
            'success' => true,
            'data' => MaterialTypeResource::collection($materialTypes),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $materialType = $this->materialTypeService->findById($id);

        if (!$materialType) {
            return response()->json([
                'success' => false,
                'message' => 'Type tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new MaterialTypeResource($materialType),
        ]);
    }

    public function store(StoreMaterialTypeRequest $request): JsonResponse
    {
        $materialType = $this->materialTypeService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Type berhasil dibuat.',
            'data' => new MaterialTypeResource($materialType),
        ], 201);
    }

    public function update(int $id, UpdateMaterialTypeRequest $request): JsonResponse
    {
        $materialType = $this->materialTypeService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Type berhasil diperbarui.',
            'data' => new MaterialTypeResource($materialType),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->materialTypeService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Type berhasil dihapus.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->materialTypeService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Type berhasil dipulihkan.',
        ]);
    }
}
