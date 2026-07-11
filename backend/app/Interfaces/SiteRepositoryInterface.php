<?php

namespace App\Interfaces;

use App\Models\Site;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface SiteRepositoryInterface
{
    public function findById(int $id): ?Site;

    public function findBySiteId(string $siteId): ?Site;

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findAll(): Collection;

    public function create(array $data): Site;

    public function update(Site $site, array $data): Site;

    public function delete(Site $site): void;

    public function restore(Site $site): void;

    public function findOnlyTrashed(int $id): ?Site;
}
