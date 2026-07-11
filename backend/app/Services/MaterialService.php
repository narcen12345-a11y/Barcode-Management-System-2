<?php

namespace App\Services;

use App\DTOs\MaterialDTO;
use App\Interfaces\MaterialRepositoryInterface;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MaterialService
{
    public function __construct(
        private readonly MaterialRepositoryInterface $materialRepository,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->materialRepository->findAllPaginated($filters, $perPage);
    }

    public function findAll(): Collection
    {
        return $this->materialRepository->findAll();
    }

    public function findById(int $id): ?Material
    {
        return $this->materialRepository->findById($id);
    }

    public function findByName(string $name): ?Material
    {
        return $this->materialRepository->findByName($name);
    }

    public function create(array $data): Material
    {
        return DB::transaction(function () use ($data) {
            $dto = MaterialDTO::fromRequest($data);
            $material = $this->materialRepository->create($dto->toArray());

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material',
                entityId: (string) $material->id,
                action: 'create_material',
                oldValues: null,
                newValues: $material->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'create_material',
                module: 'Material',
                description: "Material {$material->name} berhasil dibuat.",
            );

            return $material;
        });
    }

    public function update(int $id, array $data): Material
    {
        return DB::transaction(function () use ($id, $data) {
            $material = $this->materialRepository->findById($id);

            if (!$material) {
                throw new NotFoundHttpException('Material tidak ditemukan.');
            }

            $oldValues = $material->toArray();

            $updateData = [];
            if (isset($data['material_type_id'])) {
                $updateData['material_type_id'] = $data['material_type_id'];
            }
            if (isset($data['material_model_id'])) {
                $updateData['material_model_id'] = $data['material_model_id'];
            }
            if (isset($data['material_code'])) {
                $updateData['material_code'] = $data['material_code'];
            }
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (array_key_exists('description', $data)) {
                $updateData['description'] = $data['description'];
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = $data['is_active'];
            }

            $material = $this->materialRepository->update($material, $updateData);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material',
                entityId: (string) $material->id,
                action: 'update_material',
                oldValues: $oldValues,
                newValues: $material->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'update_material',
                module: 'Material',
                description: "Material {$material->name} berhasil diperbarui.",
            );

            return $material;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $material = $this->materialRepository->findById($id);

            if (!$material) {
                throw new NotFoundHttpException('Material tidak ditemukan.');
            }

            $oldValues = $material->toArray();

            $this->materialRepository->delete($material);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material',
                entityId: (string) $id,
                action: 'delete_material',
                oldValues: $oldValues,
                newValues: null,
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'delete_material',
                module: 'Material',
                description: "Material {$material->name} berhasil dihapus.",
            );
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $material = $this->materialRepository->findById($id);

            if (!$material) {
                $trashedMaterial = $this->materialRepository->findOnlyTrashed($id);
                if ($trashedMaterial) {
                    $this->materialRepository->restore($trashedMaterial);

                    $this->auditLogService->log(
                        userId: auth()->id(),
                        entityType: 'material',
                        entityId: (string) $id,
                        action: 'restore_material',
                        oldValues: null,
                        newValues: $trashedMaterial->fresh()->toArray(),
                    );

                    $this->activityLogService->log(
                        userId: auth()->id(),
                        activity: 'restore_material',
                        module: 'Material',
                        description: "Material {$trashedMaterial->name} berhasil dipulihkan.",
                    );

                    return;
                }
                throw new NotFoundHttpException('Material tidak ditemukan.');
            }
        });
    }
}
