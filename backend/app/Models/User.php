<?php

namespace App\Models;

use App\Enums\UserStatusEnum;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, SoftDeletes;

    protected $fillable = [
        'username',
        'email',
        'password',
        'full_name',
        'status',
        'is_active',
        'email_verified_at',
        'last_login_at',
        'password_changed_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'status' => UserStatusEnum::class,
            'email_verified_at' => 'datetime',
            'last_login_at' => 'datetime',
            'password_changed_at' => 'datetime',
        ];
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_role', 'role_id', 'permission_id')
            ->whereIn('role_id', $this->roles()->pluck('roles.id'));
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->where('name', $permissionName))
            ->exists();
    }

    public function hasAnyPermission(array $permissionNames): bool
    {
        return $this->roles()
            ->whereHas('permissions', fn($q) => $q->whereIn('name', $permissionNames))
            ->exists();
    }

    public function isSuperAdmin(): bool
    {
        return $this->hasRole('super_admin');
    }

    public function isAdmin(): bool
    {
        return $this->hasRole('admin');
    }

    public function canLogin(): bool
    {
        return $this->status === UserStatusEnum::ACTIVE->value && $this->is_active;
    }

    public function markAsLoggedIn(): void
    {
        $this->update(['last_login_at' => now()]);
    }

    public function setPassword(string $password): void
    {
        $this->update([
            'password' => Hash::make($password),
            'password_changed_at' => now(),
        ]);
    }
}
