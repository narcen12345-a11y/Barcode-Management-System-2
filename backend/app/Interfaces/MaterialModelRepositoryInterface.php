<?php

namespace App\Interfaces;

use App\Models\MaterialModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MaterialModelRepositoryInterface
{
    public function findById(int $id): ?MaterialModel;

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findAll(): Collection;

    public function findByMaterialTypeId(int $materialTypeId): Collection;

    public function create(array $data): MaterialModel;

    public function update(MaterialModel $materialModel, array $data): MaterialModel;

    public function delete(MaterialModel $materialModel): void;

    public function restore(MaterialModel $materialModel): void;

    public function findOnlyTrashed(int $id): ?MaterialModel;
}
