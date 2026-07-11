<?php

namespace App\Interfaces;

use App\Models\MaterialType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MaterialTypeRepositoryInterface
{
    public function findById(int $id): ?MaterialType;

    public function findByName(string $name): ?MaterialType;

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findAll(): Collection;

    public function create(array $data): MaterialType;

    public function update(MaterialType $materialType, array $data): MaterialType;

    public function delete(MaterialType $materialType): void;

    public function restore(MaterialType $materialType): void;

    public function findOnlyTrashed(int $id): ?MaterialType;
}
