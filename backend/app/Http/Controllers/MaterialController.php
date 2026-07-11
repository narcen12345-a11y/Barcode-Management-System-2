<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialRequest;
use App\Http\Requests\UpdateMaterialRequest;
use App\Http\Resources\MaterialResource;
use App\Services\MaterialService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function __construct(
        private readonly MaterialService $materialService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'material_type_id', 'material_model_id', 'is_active', 'trashed']);
        $perPage = $request->input('per_page', 10);

        $materials = $this->materialService->findAllPaginated($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => MaterialResource::collection($materials),
            'meta' => [
                'current_page' => $materials->currentPage(),
                'last_page' => $materials->lastPage(),
                'per_page' => $materials->perPage(),
                'total' => $materials->total(),
            ],
        ]);
    }

    public function all(): JsonResponse
    {
        $materials = $this->materialService->findAll();

        return response()->json([
            'success' => true,
            'data' => MaterialResource::collection($materials),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $material = $this->materialService->findById($id);

        if (!$material) {
            return response()->json([
                'success' => false,
                'message' => 'Material tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new MaterialResource($material),
        ]);
    }

    public function store(StoreMaterialRequest $request): JsonResponse
    {
        $material = $this->materialService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil dibuat.',
            'data' => new MaterialResource($material),
        ], 201);
    }

    public function update(int $id, UpdateMaterialRequest $request): JsonResponse
    {
        $material = $this->materialService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil diperbarui.',
            'data' => new MaterialResource($material),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->materialService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil dihapus.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->materialService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Material berhasil dipulihkan.',
        ]);
    }
}
