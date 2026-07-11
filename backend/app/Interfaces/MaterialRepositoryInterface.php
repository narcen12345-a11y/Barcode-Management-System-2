<?php

namespace App\Interfaces;

use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MaterialRepositoryInterface
{
    public function findById(int $id): ?Material;

    public function findByName(string $name): ?Material;

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findAll(): Collection;

    public function create(array $data): Material;

    public function update(Material $material, array $data): Material;

    public function delete(Material $material): void;

    public function restore(Material $material): void;

    public function findOnlyTrashed(int $id): ?Material;
}
