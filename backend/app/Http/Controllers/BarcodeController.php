<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBarcodeRequest;
use App\Http\Requests\UpdateBarcodeRequest;
use App\Http\Resources\BarcodeDetailResource;
use App\Http\Resources\BarcodeHistoryResource;
use App\Http\Resources\BarcodeResource;
use App\Services\BarcodeHistoryService;
use App\Services\BarcodeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BarcodeController extends Controller
{
    public function __construct(
        private readonly BarcodeService $barcodeService,
        private readonly BarcodeHistoryService $barcodeHistoryService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only([
            'search',
            'barcode_id',
            'serial_number',
            'site_id',
            'material_id',
            'status',
            'is_active',
            'date_from',
            'date_to',
            'trashed',
        ]);
        $perPage = $request->input('per_page', 10);

        $barcodes = $this->barcodeService->findAllPaginated($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => BarcodeResource::collection($barcodes),
            'meta' => [
                'current_page' => $barcodes->currentPage(),
                'last_page' => $barcodes->lastPage(),
                'per_page' => $barcodes->perPage(),
                'total' => $barcodes->total(),
            ],
        ]);
    }

    public function all(): JsonResponse
    {
        $barcodes = $this->barcodeService->findAll();

        return response()->json([
            'success' => true,
            'data' => BarcodeResource::collection($barcodes),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $barcode = $this->barcodeService->findById($id);

        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak ditemukan.',
            ], 404);
        }

        $barcode->load('histories.changedBy');

        return response()->json([
            'success' => true,
            'data' => new BarcodeDetailResource($barcode),
        ]);
    }

    public function showByBarcodeId(string $barcodeId): JsonResponse
    {
        $barcode = $this->barcodeService->findByBarcodeId($barcodeId);

        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak ditemukan.',
            ], 404);
        }

        $barcode->load('histories.changedBy');

        return response()->json([
            'success' => true,
            'data' => new BarcodeDetailResource($barcode),
        ]);
    }

    public function store(StoreBarcodeRequest $request): JsonResponse
    {
        $barcode = $this->barcodeService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Barcode berhasil dibuat.',
            'data' => new BarcodeResource($barcode),
        ], 201);
    }

    public function update(int $id, UpdateBarcodeRequest $request): JsonResponse
    {
        $barcode = $this->barcodeService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Barcode berhasil diperbarui.',
            'data' => new BarcodeResource($barcode),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->barcodeService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Barcode berhasil dihapus.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->barcodeService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Barcode berhasil dipulihkan.',
        ]);
    }

    public function history(int $id, Request $request): JsonResponse
    {
        $barcode = $this->barcodeService->findById($id);

        if (!$barcode) {
            return response()->json([
                'success' => false,
                'message' => 'Barcode tidak ditemukan.',
            ], 404);
        }

        $filters = $request->only(['field_name', 'changed_by', 'date_from', 'date_to']);
        $perPage = $request->input('per_page', 10);

        $histories = $this->barcodeHistoryService->findAllPaginatedByBarcode($id, $filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => BarcodeHistoryResource::collection($histories),
            'meta' => [
                'current_page' => $histories->currentPage(),
                'last_page' => $histories->lastPage(),
                'per_page' => $histories->perPage(),
                'total' => $histories->total(),
            ],
        ]);
    }
}
