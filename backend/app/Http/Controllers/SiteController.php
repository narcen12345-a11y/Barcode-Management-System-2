<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreSiteRequest;
use App\Http\Requests\UpdateSiteRequest;
use App\Http\Resources\SiteResource;
use App\Services\SiteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function __construct(
        private readonly SiteService $siteService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filters = $request->only(['search', 'site_id', 'site_name', 'is_active', 'trashed']);
        $perPage = $request->input('per_page', 10);

        $sites = $this->siteService->findAllPaginated($filters, $perPage);

        return response()->json([
            'success' => true,
            'data' => SiteResource::collection($sites),
            'meta' => [
                'current_page' => $sites->currentPage(),
                'last_page' => $sites->lastPage(),
                'per_page' => $sites->perPage(),
                'total' => $sites->total(),
            ],
        ]);
    }

    public function all(): JsonResponse
    {
        $sites = $this->siteService->findAll();

        return response()->json([
            'success' => true,
            'data' => SiteResource::collection($sites),
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $site = $this->siteService->findById($id);

        if (!$site) {
            return response()->json([
                'success' => false,
                'message' => 'Site tidak ditemukan.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new SiteResource($site),
        ]);
    }

    public function store(StoreSiteRequest $request): JsonResponse
    {
        $site = $this->siteService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Site berhasil dibuat.',
            'data' => new SiteResource($site),
        ], 201);
    }

    public function update(int $id, UpdateSiteRequest $request): JsonResponse
    {
        $site = $this->siteService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Site berhasil diperbarui.',
            'data' => new SiteResource($site),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->siteService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Site berhasil dihapus.',
        ]);
    }

    public function restore(int $id): JsonResponse
    {
        $this->siteService->restore($id);

        return response()->json([
            'success' => true,
            'message' => 'Site berhasil dipulihkan.',
        ]);
    }
}
