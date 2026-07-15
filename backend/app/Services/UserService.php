<?php

namespace App\Services;

use App\DTOs\RegisterUserDTO;
use App\DTOs\VerifyUserDTO;
use App\Enums\UserStatusEnum;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly RoleRepositoryInterface $roleRepository,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->userRepository->findAllPaginated($filters, $perPage);
    }

    public function findAll(): Collection
    {
        return $this->userRepository->findAll();
    }

    public function findById(int $id): ?User
    {
        return $this->userRepository->findById($id);
    }

    public function create(RegisterUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->userRepository->create([
                'username' => $dto->username,
                'email' => $dto->email,
                'password' => Hash::make($dto->password),
                'full_name' => $dto->fullName,
                'status' => UserStatusEnum::PENDING_VERIFICATION->value,
                'is_active' => false,
            ]);

            if (!empty($dto->roleIds)) {
                $user->roles()->sync($dto->roleIds);
            }

            $user->load('roles');

            // Audit Log: Pembuatan Akun
            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'create_user',
                oldValues: null,
                newValues: $user->toArray(),
            );

            // Activity Log: create_user
            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'create_user',
                module: 'User Management',
                description: "User {$user->username} berhasil dibuat.",
            );

            return $user;
        });
    }

    private function ensureNotSuperAdmin(User $user): void
    {
        if ($user->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            throw new AccessDeniedHttpException('Anda tidak memiliki izin untuk mengubah Super Admin.');
        }
    }

    public function update(int $id, array $data): User
    {
        return DB::transaction(function () use ($id, $data) {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                throw new NotFoundHttpException('User tidak ditemukan.');
            }

            $this->ensureNotSuperAdmin($user);

            $oldValues = $user->toArray();
            $updateData = [];

            if (isset($data['username'])) {
                $updateData['username'] = $data['username'];
            }
            if (isset($data['email'])) {
                $updateData['email'] = $data['email'];
            }
            if (isset($data['full_name'])) {
                $updateData['full_name'] = $data['full_name'];
            }
            if (isset($data['password'])) {
                $updateData['password'] = Hash::make($data['password']);
            }

            if (!empty($updateData)) {
                $this->userRepository->update($user, $updateData);
            }

            if (isset($data['role_ids'])) {
                $user->roles()->sync($data['role_ids']);
            }

            $user = $user->fresh()->load('roles');

            // Audit Log: Update User
            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'update_user',
                oldValues: $oldValues,
                newValues: $user->toArray(),
            );

            // Activity Log: update_user
            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'update_user',
                module: 'User Management',
                description: "User {$user->username} berhasil diperbarui.",
            );

            return $user;
        });
    }

    public function verify(VerifyUserDTO $dto): User
    {
        return DB::transaction(function () use ($dto) {
            $user = $this->userRepository->findById($dto->userId);

            if (!$user) {
                throw new NotFoundHttpException('User tidak ditemukan.');
            }

            $this->ensureNotSuperAdmin($user);

            $oldValues = $user->toArray();
            $isActive = $dto->status === UserStatusEnum::ACTIVE->value;

            $this->userRepository->update($user, [
                'status' => $dto->status,
                'is_active' => $isActive,
                'email_verified_at' => $isActive ? now() : $user->email_verified_at,
            ]);

            $user = $user->fresh();

            // Audit Log: Verifikasi Akun
            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'verify_user',
                oldValues: $oldValues,
                newValues: $user->toArray(),
            );

            // Activity Log: verify_user
            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'verify_user',
                module: 'User Management',
                description: "User {$user->username} berhasil diverifikasi menjadi {$dto->status}.",
            );

            return $user;
        });
    }

    public function activate(int $id): User
    {
        return DB::transaction(function () use ($id) {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                throw new NotFoundHttpException('User tidak ditemukan.');
            }

            $this->ensureNotSuperAdmin($user);

            $oldValues = $user->toArray();

            $this->userRepository->update($user, [
                'status' => UserStatusEnum::ACTIVE->value,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);

            $user = $user->fresh();

            // Audit Log: Aktivasi Akun
            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'activate_user',
                oldValues: $oldValues,
                newValues: $user->toArray(),
            );

            // Activity Log: activate_user
            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'activate_user',
                module: 'User Management',
                description: "User {$user->username} berhasil diaktifkan.",
            );

            return $user;
        });
    }

    public function deactivate(int $id): User
    {
        return DB::transaction(function () use ($id) {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                throw new NotFoundHttpException('User tidak ditemukan.');
            }

            $this->ensureNotSuperAdmin($user);

            $oldValues = $user->toArray();

            $this->userRepository->update($user, [
                'status' => UserStatusEnum::INACTIVE->value,
                'is_active' => false,
            ]);

            $user = $user->fresh();

            // Audit Log: Penonaktifan Akun
            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'deactivate_user',
                oldValues: $oldValues,
                newValues: $user->toArray(),
            );

            // Activity Log: deactivate_user
            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'deactivate_user',
                module: 'User Management',
                description: "User {$user->username} berhasil dinonaktifkan.",
            );

            return $user;
        });
    }

    public function resetPassword(int $id): string
    {
        return DB::transaction(function () use ($id) {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                throw new NotFoundHttpException('User tidak ditemukan.');
            }

            $this->ensureNotSuperAdmin($user);

            $newPassword = bin2hex(random_bytes(6)); // 12-character hex string, cryptographically secure
            $user->setPassword($newPassword);

            // Audit Log: Reset Password
            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'reset_password',
                oldValues: ['password' => '[redacted]'],
                newValues: ['password' => '[redacted]'],
            );

            // Activity Log: reset_password
            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'reset_password',
                module: 'User Management',
                description: "Password user {$user->username} berhasil di-reset.",
            );

            return $newPassword;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                throw new NotFoundHttpException('User tidak ditemukan.');
            }

            $this->ensureNotSuperAdmin($user);

            $oldValues = $user->toArray();

            $this->userRepository->delete($user);

            // Audit Log: Soft Delete User
            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'user',
                entityId: (string) $id,
                action: 'delete_user',
                oldValues: $oldValues,
                newValues: null,
            );

            // Activity Log: delete_user
            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'delete_user',
                module: 'User Management',
                description: "User {$user->username} berhasil dihapus.",
            );
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $user = $this->userRepository->findById($id);

            if (!$user) {
                $trashedUser = $this->userRepository->findOnlyTrashedById($id);
                if ($trashedUser) {
                    $this->userRepository->restore($trashedUser);

                    // Audit Log: Restore User
                    $this->auditLogService->log(
                        userId: auth()->id(),
                        entityType: 'user',
                        entityId: (string) $id,
                        action: 'restore_user',
                        oldValues: null,
                        newValues: $trashedUser->fresh()->toArray(),
                    );

                    // Activity Log: restore_user
                    $this->activityLogService->log(
                        userId: auth()->id(),
                        activity: 'restore_user',
                        module: 'User Management',
                        description: "User {$trashedUser->username} berhasil dipulihkan.",
                    );

                    return;
                }
                throw new NotFoundHttpException('User tidak ditemukan.');
            }
        });
    }

    public function countByStatus(string $status): int
    {
        return $this->userRepository->countByStatus($status);
    }
}
