<?php

namespace App\Repositories;

use App\Interfaces\MaterialTypeRepositoryInterface;
use App\Models\MaterialType;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MaterialTypeRepository implements MaterialTypeRepositoryInterface
{
    public function findById(int $id): ?MaterialType
    {
        return MaterialType::find($id);
    }

    public function findByName(string $name): ?MaterialType
    {
        return MaterialType::where('name', $name)->first();
    }

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = MaterialType::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['trashed']) && $filters['trashed'] === 'only') {
            $query->onlyTrashed();
        } elseif (!empty($filters['trashed']) && $filters['trashed'] === 'with') {
            $query->withTrashed();
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function findAll(): Collection
    {
        return MaterialType::all();
    }

    public function create(array $data): MaterialType
    {
        return MaterialType::create($data);
    }

    public function update(MaterialType $materialType, array $data): MaterialType
    {
        $materialType->update($data);
        return $materialType->fresh();
    }

    public function delete(MaterialType $materialType): void
    {
        $materialType->delete();
    }

    public function restore(MaterialType $materialType): void
    {
        $materialType->restore();
    }

    public function findOnlyTrashed(int $id): ?MaterialType
    {
        return MaterialType::onlyTrashed()->find($id);
    }
}
