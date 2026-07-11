<?php

namespace App\Services;

use App\DTOs\MaterialTypeDTO;
use App\Interfaces\MaterialTypeRepositoryInterface;
use App\Models\MaterialType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MaterialTypeService
{
    public function __construct(
        private readonly MaterialTypeRepositoryInterface $materialTypeRepository,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->materialTypeRepository->findAllPaginated($filters, $perPage);
    }

    public function findAll(): Collection
    {
        return $this->materialTypeRepository->findAll();
    }

    public function findById(int $id): ?MaterialType
    {
        return $this->materialTypeRepository->findById($id);
    }

    public function findByName(string $name): ?MaterialType
    {
        return $this->materialTypeRepository->findByName($name);
    }

    public function create(array $data): MaterialType
    {
        return DB::transaction(function () use ($data) {
            $dto = MaterialTypeDTO::fromRequest($data);
            $materialType = $this->materialTypeRepository->create($dto->toArray());

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material_type',
                entityId: (string) $materialType->id,
                action: 'create_material_type',
                oldValues: null,
                newValues: $materialType->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'create_material_type',
                module: 'Material Type',
                description: "Material Type {$materialType->name} berhasil dibuat.",
            );

            return $materialType;
        });
    }

    public function update(int $id, array $data): MaterialType
    {
        return DB::transaction(function () use ($id, $data) {
            $materialType = $this->materialTypeRepository->findById($id);

            if (!$materialType) {
                throw new NotFoundHttpException('Type tidak ditemukan.');
            }

            $oldValues = $materialType->toArray();

            $updateData = [];
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (array_key_exists('description', $data)) {
                $updateData['description'] = $data['description'];
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = $data['is_active'];
            }

            $materialType = $this->materialTypeRepository->update($materialType, $updateData);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material_type',
                entityId: (string) $materialType->id,
                action: 'update_material_type',
                oldValues: $oldValues,
                newValues: $materialType->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'update_material_type',
                module: 'Material Type',
                description: "Material Type {$materialType->name} berhasil diperbarui.",
            );

            return $materialType;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $materialType = $this->materialTypeRepository->findById($id);

            if (!$materialType) {
                throw new NotFoundHttpException('Type tidak ditemukan.');
            }

            $oldValues = $materialType->toArray();

            $this->materialTypeRepository->delete($materialType);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material_type',
                entityId: (string) $id,
                action: 'delete_material_type',
                oldValues: $oldValues,
                newValues: null,
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'delete_material_type',
                module: 'Material Type',
                description: "Material Type {$materialType->name} berhasil dihapus.",
            );
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $materialType = $this->materialTypeRepository->findById($id);

            if (!$materialType) {
                $trashedType = $this->materialTypeRepository->findOnlyTrashed($id);
                if ($trashedType) {
                    $this->materialTypeRepository->restore($trashedType);

                    $this->auditLogService->log(
                        userId: auth()->id(),
                        entityType: 'material_type',
                        entityId: (string) $id,
                        action: 'restore_material_type',
                        oldValues: null,
                        newValues: $trashedType->fresh()->toArray(),
                    );

                    $this->activityLogService->log(
                        userId: auth()->id(),
                        activity: 'restore_material_type',
                        module: 'Material Type',
                        description: "Material Type {$trashedType->name} berhasil dipulihkan.",
                    );

                    return;
                }
                throw new NotFoundHttpException('Type tidak ditemukan.');
            }
        });
    }
}
