<?php

namespace App\Services;

use App\Interfaces\PermissionRepositoryInterface;
use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PermissionService
{
    public function __construct(
        private readonly PermissionRepositoryInterface $permissionRepository,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->permissionRepository->findAllPaginated($filters, $perPage);
    }

    public function findAll(): Collection
    {
        return $this->permissionRepository->findAll();
    }

    public function findById(int $id): ?Permission
    {
        return $this->permissionRepository->findById($id);
    }

    public function findByName(string $name): ?Permission
    {
        return $this->permissionRepository->findByName($name);
    }

    public function findByModule(string $module): Collection
    {
        return $this->permissionRepository->findByModule($module);
    }

    public function create(array $data): Permission
    {
        return DB::transaction(function () use ($data) {
            $permission = $this->permissionRepository->create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'module' => $data['module'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'permission',
                entityId: (string) $permission->id,
                action: 'create_permission',
                oldValues: null,
                newValues: $permission->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'create_permission',
                module: 'Permission Management',
                description: "Permission {$permission->name} berhasil dibuat.",
            );

            return $permission;
        });
    }

    public function update(int $id, array $data): Permission
    {
        return DB::transaction(function () use ($id, $data) {
            $permission = $this->permissionRepository->findById($id);

            if (!$permission) {
                throw new NotFoundHttpException('Permission tidak ditemukan.');
            }

            $oldValues = $permission->toArray();

            $updateData = [];
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (isset($data['display_name'])) {
                $updateData['display_name'] = $data['display_name'];
            }
            if (isset($data['module'])) {
                $updateData['module'] = $data['module'];
            }
            if (isset($data['description'])) {
                $updateData['description'] = $data['description'];
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = $data['is_active'];
            }

            $permission = $this->permissionRepository->update($permission, $updateData);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'permission',
                entityId: (string) $permission->id,
                action: 'update_permission',
                oldValues: $oldValues,
                newValues: $permission->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'update_permission',
                module: 'Permission Management',
                description: "Permission {$permission->name} berhasil diperbarui.",
            );

            return $permission;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $permission = $this->permissionRepository->findById($id);

            if (!$permission) {
                throw new NotFoundHttpException('Permission tidak ditemukan.');
            }

            $oldValues = $permission->toArray();

            $this->permissionRepository->delete($permission);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'permission',
                entityId: (string) $id,
                action: 'delete_permission',
                oldValues: $oldValues,
                newValues: null,
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'delete_permission',
                module: 'Permission Management',
                description: "Permission {$permission->name} berhasil dihapus.",
            );
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $permission = $this->permissionRepository->findById($id);

            if (!$permission) {
                $trashedPermission = $this->permissionRepository->findOnlyTrashed($id);
                if ($trashedPermission) {
                    $this->permissionRepository->restore($trashedPermission);

                    $this->auditLogService->log(
                        userId: auth()->id(),
                        entityType: 'permission',
                        entityId: (string) $id,
                        action: 'restore_permission',
                        oldValues: null,
                        newValues: $trashedPermission->fresh()->toArray(),
                    );

                    $this->activityLogService->log(
                        userId: auth()->id(),
                        activity: 'restore_permission',
                        module: 'Permission Management',
                        description: "Permission {$trashedPermission->name} berhasil dipulihkan.",
                    );

                    return;
                }
                throw new NotFoundHttpException('Permission tidak ditemukan.');
            }
        });
    }
}
