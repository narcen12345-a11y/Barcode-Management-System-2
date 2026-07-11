<?php

namespace App\Repositories;

use App\Interfaces\MaterialModelRepositoryInterface;
use App\Models\MaterialModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MaterialModelRepository implements MaterialModelRepositoryInterface
{
    public function findById(int $id): ?MaterialModel
    {
        return MaterialModel::find($id);
    }

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = MaterialModel::query()->with('materialType');

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['material_type_id'])) {
            $query->where('material_type_id', $filters['material_type_id']);
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
        return MaterialModel::with('materialType')->get();
    }

    public function findByMaterialTypeId(int $materialTypeId): Collection
    {
        return MaterialModel::with('materialType')
            ->where('material_type_id', $materialTypeId)
            ->get();
    }

    public function create(array $data): MaterialModel
    {
        return MaterialModel::create($data);
    }

    public function update(MaterialModel $materialModel, array $data): MaterialModel
    {
        $materialModel->update($data);
        return $materialModel->fresh();
    }

    public function delete(MaterialModel $materialModel): void
    {
        $materialModel->delete();
    }

    public function restore(MaterialModel $materialModel): void
    {
        $materialModel->restore();
    }

    public function findOnlyTrashed(int $id): ?MaterialModel
    {
        return MaterialModel::onlyTrashed()->find($id);
    }
}
