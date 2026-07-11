<?php

namespace App\Services;

use App\Interfaces\RoleRepositoryInterface;
use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class RoleService
{
    public function __construct(
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->roleRepository->findAllPaginated($filters, $perPage);
    }

    public function findAll(): Collection
    {
        return $this->roleRepository->findAll();
    }

    public function findById(int $id): ?Role
    {
        return $this->roleRepository->findById($id);
    }

    public function findByName(string $name): ?Role
    {
        return $this->roleRepository->findByName($name);
    }

    public function create(array $data): Role
    {
        return DB::transaction(function () use ($data) {
            $role = $this->roleRepository->create([
                'name' => $data['name'],
                'display_name' => $data['display_name'],
                'description' => $data['description'] ?? null,
                'is_active' => $data['is_active'] ?? true,
            ]);

            if (!empty($data['permission_ids'])) {
                $role->permissions()->sync($data['permission_ids']);
            }

            $role->load('permissions');

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'role',
                entityId: (string) $role->id,
                action: 'create_role',
                oldValues: null,
                newValues: $role->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'create_role',
                module: 'Role Management',
                description: "Role {$role->name} berhasil dibuat.",
            );

            return $role;
        });
    }

    public function update(int $id, array $data): Role
    {
        return DB::transaction(function () use ($id, $data) {
            $role = $this->roleRepository->findById($id);

            if (!$role) {
                throw new NotFoundHttpException('Role tidak ditemukan.');
            }

            $oldValues = $role->load('permissions')->toArray();

            $updateData = [];
            if (isset($data['name'])) {
                $updateData['name'] = $data['name'];
            }
            if (isset($data['display_name'])) {
                $updateData['display_name'] = $data['display_name'];
            }
            if (isset($data['description'])) {
                $updateData['description'] = $data['description'];
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = $data['is_active'];
            }

            if (!empty($updateData)) {
                $this->roleRepository->update($role, $updateData);
            }

            if (isset($data['permission_ids'])) {
                $role->permissions()->sync($data['permission_ids']);
            }

            $role = $role->fresh()->load('permissions');

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'role',
                entityId: (string) $role->id,
                action: 'update_role',
                oldValues: $oldValues,
                newValues: $role->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'update_role',
                module: 'Role Management',
                description: "Role {$role->name} berhasil diperbarui.",
            );

            return $role;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $role = $this->roleRepository->findById($id);

            if (!$role) {
                throw new NotFoundHttpException('Role tidak ditemukan.');
            }

            $oldValues = $role->toArray();

            $this->roleRepository->delete($role);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'role',
                entityId: (string) $id,
                action: 'delete_role',
                oldValues: $oldValues,
                newValues: null,
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'delete_role',
                module: 'Role Management',
                description: "Role {$role->name} berhasil dihapus.",
            );
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $role = $this->roleRepository->findById($id);

            if (!$role) {
                $trashedRole = $this->roleRepository->findOnlyTrashed($id);
                if ($trashedRole) {
                    $this->roleRepository->restore($trashedRole);

                    $this->auditLogService->log(
                        userId: auth()->id(),
                        entityType: 'role',
                        entityId: (string) $id,
                        action: 'restore_role',
                        oldValues: null,
                        newValues: $trashedRole->fresh()->toArray(),
                    );

                    $this->activityLogService->log(
                        userId: auth()->id(),
                        activity: 'restore_role',
                        module: 'Role Management',
                        description: "Role {$trashedRole->name} berhasil dipulihkan.",
                    );

                    return;
                }
                throw new NotFoundHttpException('Role tidak ditemukan.');
            }
        });
    }
}
