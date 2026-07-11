<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMaterialModelRequest;
use App\Http\Requests\UpdateMaterialModelRequest;
use App\Http\Resources\MaterialModelResource;
use App\Services\MaterialModelService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MaterialModelController extends Controller
{
    public function __construct(
        private readonly MaterialModelService $materialModelService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'material_type_id', 'is_active', 'trashed']);
        $perPage = $request->input('per_page', 10);

        $materialModels = $this->materialModelService->findAllPaginated($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => MaterialModelResource::collection($materialModels),
            'meta' => [
                'current_page' => $materialModels->currentPage(),
                'last_page' => $materialModels->lastPage(),
                'per_page' => $materialModels->perPage(),
                'total' => $materialModels->total(),
            ],
        ]);
    }

    public function all(): JsonResponse
    {
        $materialModels = $this->materialModelService->findAll();

        return response()->json([
            'success' => true,
            'data' => MaterialModelResource::collection($materialModels),
        ]);
    }

    public function byMaterialType(int $materialTypeId): JsonResponse
    {
        $materialModels = $this->materialModelService->findByMaterialTypeId($materialTypeId);

        return response()->json([
            'success' => true,
            'data' => MaterialModelResource::collection($materialModels),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $materialModel = $this->materialModelService->findById($id);

        if (!$materialModel) {
            return response()->json([
                'success' => false,
                'message' => 'Model tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new MaterialModelResource($materialModel),
        ]);
    }

    public function store(StoreMaterialModelRequest $request): JsonResponse
    {
        $materialModel = $this->materialModelService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Model berhasil dibuat.',
            'data' => new MaterialModelResource($materialModel),
        ], 201);
    }

    public function update(int $id, UpdateMaterialModelRequest $request): JsonResponse
    {
        $materialModel = $this->materialModelService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Model berhasil diperbarui.',
            'data' => new MaterialModelResource($materialModel),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->materialModelService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Model berhasil dihapus.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->materialModelService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Model berhasil dipulihkan.',
        ]);
    }
}
