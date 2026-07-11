<?php

namespace App\Repositories;

use App\Interfaces\MaterialRepositoryInterface;
use App\Models\Material;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MaterialRepository implements MaterialRepositoryInterface
{
    public function findById(int $id): ?Material
    {
        return Material::find($id);
    }

    public function findByName(string $name): ?Material
    {
        return Material::where('name', $name)->first();
    }

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Material::query()->with(['materialType', 'materialModel']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('material_code', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['material_type_id'])) {
            $query->where('material_type_id', $filters['material_type_id']);
        }

        if (!empty($filters['material_model_id'])) {
            $query->where('material_model_id', $filters['material_model_id']);
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
        return Material::with(['materialType', 'materialModel'])->get();
    }

    public function create(array $data): Material
    {
        return Material::create($data);
    }

    public function update(Material $material, array $data): Material
    {
        $material->update($data);
        return $material->fresh();
    }

    public function delete(Material $material): void
    {
        $material->delete();
    }

    public function restore(Material $material): void
    {
        $material->restore();
    }

    public function findOnlyTrashed(int $id): ?Material
    {
        return Material::onlyTrashed()->find($id);
    }
}
