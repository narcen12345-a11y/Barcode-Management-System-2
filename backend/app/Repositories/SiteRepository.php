<?php

namespace App\Repositories;

use App\Interfaces\SiteRepositoryInterface;
use App\Models\Site;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class SiteRepository implements SiteRepositoryInterface
{
    public function findById(int $id): ?Site
    {
        return Site::find($id);
    }

    public function findBySiteId(string $siteId): ?Site
    {
        return Site::where('site_id', $siteId)->first();
    }

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Site::query();

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('site_id', 'like', "%{$search}%")
                    ->orWhere('site_name', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['site_id'])) {
            $query->where('site_id', 'like', "%{$filters['site_id']}%");
        }

        if (!empty($filters['site_name'])) {
            $query->where('site_name', 'like', "%{$filters['site_name']}%");
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
        return Site::all();
    }

    public function create(array $data): Site
    {
        return Site::create($data);
    }

    public function update(Site $site, array $data): Site
    {
        $site->update($data);
        return $site->fresh();
    }

    public function delete(Site $site): void
    {
        $site->delete();
    }

    public function restore(Site $site): void
    {
        $site->restore();
    }

    public function findOnlyTrashed(int $id): ?Site
    {
        return Site::onlyTrashed()->find($id);
    }
}
