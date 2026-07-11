<?php

namespace App\Interfaces;

use App\Models\Role;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface RoleRepositoryInterface
{
    public function findById(int $id): ?Role;

    public function findByName(string $name): ?Role;

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findAll(): Collection;

    public function create(array $data): Role;

    public function update(Role $role, array $data): Role;

    public function delete(Role $role): void;

    public function restore(Role $role): void;

    public function findOnlyTrashed(int $id): ?Role;
}
