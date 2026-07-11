<?php

namespace App\Services;

use App\DTOs\MaterialModelDTO;
use App\Interfaces\MaterialModelRepositoryInterface;
use App\Models\MaterialModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MaterialModelService
{
    public function __construct(
        private readonly MaterialModelRepositoryInterface $materialModelRepository,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->materialModelRepository->findAllPaginated($filters, $perPage);
    }

    public function findAll(): Collection
    {
        return $this->materialModelRepository->findAll();
    }

    public function findById(int $id): ?MaterialModel
    {
        return $this->materialModelRepository->findById($id);
    }

    public function findByMaterialTypeId(int $materialTypeId): Collection
    {
        return $this->materialModelRepository->findByMaterialTypeId($materialTypeId);
    }

    public function create(array $data): MaterialModel
    {
        return DB::transaction(function () use ($data) {
            $dto = MaterialModelDTO::fromRequest($data);
            $materialModel = $this->materialModelRepository->create($dto->toArray());

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material_model',
                entityId: (string) $materialModel->id,
                action: 'create_material_model',
                oldValues: null,
                newValues: $materialModel->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'create_material_model',
                module: 'Material Model',
                description: "Material Model {$materialModel->name} berhasil dibuat.",
            );

            return $materialModel;
        });
    }

    public function update(int $id, array $data): MaterialModel
    {
        return DB::transaction(function () use ($id, $data) {
            $materialModel = $this->materialModelRepository->findById($id);

            if (!$materialModel) {
                throw new NotFoundHttpException('Model tidak ditemukan.');
            }

            $oldValues = $materialModel->toArray();

            $updateData = [];
            if (isset($data['material_type_id'])) {
                $updateData['material_type_id'] = $data['material_type_id'];
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

            $materialModel = $this->materialModelRepository->update($materialModel, $updateData);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material_model',
                entityId: (string) $materialModel->id,
                action: 'update_material_model',
                oldValues: $oldValues,
                newValues: $materialModel->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'update_material_model',
                module: 'Material Model',
                description: "Material Model {$materialModel->name} berhasil diperbarui.",
            );

            return $materialModel;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $materialModel = $this->materialModelRepository->findById($id);

            if (!$materialModel) {
                throw new NotFoundHttpException('Model tidak ditemukan.');
            }

            $oldValues = $materialModel->toArray();

            $this->materialModelRepository->delete($materialModel);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'material_model',
                entityId: (string) $id,
                action: 'delete_material_model',
                oldValues: $oldValues,
                newValues: null,
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'delete_material_model',
                module: 'Material Model',
                description: "Material Model {$materialModel->name} berhasil dihapus.",
            );
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $materialModel = $this->materialModelRepository->findById($id);

            if (!$materialModel) {
                $trashedModel = $this->materialModelRepository->findOnlyTrashed($id);
                if ($trashedModel) {
                    $this->materialModelRepository->restore($trashedModel);

                    $this->auditLogService->log(
                        userId: auth()->id(),
                        entityType: 'material_model',
                        entityId: (string) $id,
                        action: 'restore_material_model',
                        oldValues: null,
                        newValues: $trashedModel->fresh()->toArray(),
                    );

                    $this->activityLogService->log(
                        userId: auth()->id(),
                        activity: 'restore_material_model',
                        module: 'Material Model',
                        description: "Material Model {$trashedModel->name} berhasil dipulihkan.",
                    );

                    return;
                }
                throw new NotFoundHttpException('Model tidak ditemukan.');
            }
        });
    }
}
