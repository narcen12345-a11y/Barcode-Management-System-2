<?php

namespace App\Interfaces;

use App\Models\Permission;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface PermissionRepositoryInterface
{
    public function findById(int $id): ?Permission;

    public function findByName(string $name): ?Permission;

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findAll(): Collection;

    public function findByModule(string $module): Collection;

    public function create(array $data): Permission;

    public function update(Permission $permission, array $data): Permission;

    public function delete(Permission $permission): void;

    public function restore(Permission $permission): void;

    public function findOnlyTrashed(int $id): ?Permission;
}
