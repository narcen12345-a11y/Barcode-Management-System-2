<?php

namespace App\Services;

use App\DTOs\ChangePasswordDTO;
use App\DTOs\LoginRequestDTO;
use App\Interfaces\UserRepositoryInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class AuthenticationService
{
    public function __construct(
        private readonly UserRepositoryInterface $userRepository,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function login(LoginRequestDTO $dto): array
    {
        $user = $this->userRepository->findByLogin($dto->login);

        if (!$user) {
            throw new UnauthorizedHttpException('', 'Terdapat kesalahan Username atau Password.');
        }

        if (!$user->canLogin()) {
            $message = match ($user->status->value) {
                'pending_verification' => 'Akun Anda belum diverifikasi oleh Administrator.',
                'inactive' => 'Akun Anda sedang dinonaktifkan.',
                'suspended' => 'Akun Anda sedang ditangguhkan.',
                default => 'Akun Anda tidak dapat mengakses sistem.',
            };
            throw new UnauthorizedHttpException('', $message);
        }

        if (!Hash::check($dto->password, $user->password)) {
            // Audit Log: Gagal Login
            $this->auditLogService->log(
                userId: $user->id,
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'failed_login',
                oldValues: null,
                newValues: null,
            );
            throw new UnauthorizedHttpException('', 'Terdapat kesalahan Username atau Password.');
        }

        DB::transaction(function () use ($user) {
            $user->markAsLoggedIn();

            // Audit Log: Login berhasil
            $this->auditLogService->log(
                userId: $user->id,
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'login',
                oldValues: null,
                newValues: null,
            );

            // Activity Log: login
            $this->activityLogService->log(
                userId: $user->id,
                activity: 'login',
                module: 'Authentication',
                description: "User {$user->username} berhasil login.",
            );
        });

        $token = $user->createToken('auth-token')->plainTextToken;

        return [
            'user' => $user->load('roles'),
            'token' => $token,
        ];
    }

    public function logout(User $user): void
    {
        DB::transaction(function () use ($user) {
            $token = $user->currentAccessToken();

            // Audit Log: Logout
            $this->auditLogService->log(
                userId: $user->id,
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'logout',
                oldValues: null,
                newValues: null,
            );

            // Activity Log: logout
            $this->activityLogService->log(
                userId: $user->id,
                activity: 'logout',
                module: 'Authentication',
                description: "User {$user->username} berhasil logout.",
            );

            $token->delete();
        });
    }

    public function changePassword(ChangePasswordDTO $dto): void
    {
        $user = $this->userRepository->findById($dto->userId);

        if (!$user) {
            throw new UnauthorizedHttpException('', 'User tidak ditemukan.');
        }

        if (!Hash::check($dto->currentPassword, $user->password)) {
            throw new UnauthorizedHttpException('', 'Password lama tidak sesuai.');
        }

        DB::transaction(function () use ($user, $dto) {
            $oldPasswordHash = $user->password;
            $user->setPassword($dto->newPassword);

            // Audit Log: Perubahan Password
            $this->auditLogService->log(
                userId: $user->id,
                entityType: 'user',
                entityId: (string) $user->id,
                action: 'change_password',
                oldValues: ['password' => '[redacted]'],
                newValues: ['password' => '[redacted]'],
            );

            // Activity Log: change_password
            $this->activityLogService->log(
                userId: $user->id,
                activity: 'change_password',
                module: 'Authentication',
                description: "User {$user->username} mengubah password.",
            );
        });
    }

    public function getCurrentUser(User $user): User
    {
        return $user->load(['roles.permissions']);
    }
}
