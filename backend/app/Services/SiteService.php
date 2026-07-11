<?php

namespace App\Services;

use App\DTOs\SiteDTO;
use App\Interfaces\SiteRepositoryInterface;
use App\Models\Site;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SiteService
{
    public function __construct(
        private readonly SiteRepositoryInterface $siteRepository,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->siteRepository->findAllPaginated($filters, $perPage);
    }

    public function findAll(): Collection
    {
        return $this->siteRepository->findAll();
    }

    public function findById(int $id): ?Site
    {
        return $this->siteRepository->findById($id);
    }

    public function findBySiteId(string $siteId): ?Site
    {
        return $this->siteRepository->findBySiteId($siteId);
    }

    public function create(array $data): Site
    {
        return DB::transaction(function () use ($data) {
            $dto = SiteDTO::fromRequest($data);
            $site = $this->siteRepository->create($dto->toArray());

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'site',
                entityId: (string) $site->id,
                action: 'create_site',
                oldValues: null,
                newValues: $site->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'create_site',
                module: 'Site',
                description: "Site {$site->site_name} berhasil dibuat.",
            );

            return $site;
        });
    }

    public function update(int $id, array $data): Site
    {
        return DB::transaction(function () use ($id, $data) {
            $site = $this->siteRepository->findById($id);

            if (!$site) {
                throw new NotFoundHttpException('Site tidak ditemukan.');
            }

            $oldValues = $site->toArray();

            $updateData = [];
            if (isset($data['site_id'])) {
                $updateData['site_id'] = $data['site_id'];
            }
            if (isset($data['site_name'])) {
                $updateData['site_name'] = $data['site_name'];
            }
            if (array_key_exists('region', $data)) {
                $updateData['region'] = $data['region'];
            }
            if (array_key_exists('address', $data)) {
                $updateData['address'] = $data['address'];
            }
            if (array_key_exists('latitude', $data)) {
                $updateData['latitude'] = $data['latitude'];
            }
            if (array_key_exists('longitude', $data)) {
                $updateData['longitude'] = $data['longitude'];
            }
            if (isset($data['is_active'])) {
                $updateData['is_active'] = $data['is_active'];
            }

            $site = $this->siteRepository->update($site, $updateData);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'site',
                entityId: (string) $site->id,
                action: 'update_site',
                oldValues: $oldValues,
                newValues: $site->toArray(),
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'update_site',
                module: 'Site',
                description: "Site {$site->site_name} berhasil diperbarui.",
            );

            return $site;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $site = $this->siteRepository->findById($id);

            if (!$site) {
                throw new NotFoundHttpException('Site tidak ditemukan.');
            }

            $oldValues = $site->toArray();

            $this->siteRepository->delete($site);

            $this->auditLogService->log(
                userId: auth()->id(),
                entityType: 'site',
                entityId: (string) $id,
                action: 'delete_site',
                oldValues: $oldValues,
                newValues: null,
            );

            $this->activityLogService->log(
                userId: auth()->id(),
                activity: 'delete_site',
                module: 'Site',
                description: "Site {$site->site_name} berhasil dihapus.",
            );
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $site = $this->siteRepository->findById($id);

            if (!$site) {
                $trashedSite = $this->siteRepository->findOnlyTrashed($id);
                if ($trashedSite) {
                    $this->siteRepository->restore($trashedSite);

                    $this->auditLogService->log(
                        userId: auth()->id(),
                        entityType: 'site',
                        entityId: (string) $id,
                        action: 'restore_site',
                        oldValues: null,
                        newValues: $trashedSite->fresh()->toArray(),
                    );

                    $this->activityLogService->log(
                        userId: auth()->id(),
                        activity: 'restore_site',
                        module: 'Site',
                        description: "Site {$trashedSite->site_name} berhasil dipulihkan.",
                    );

                    return;
                }
                throw new NotFoundHttpException('Site tidak ditemukan.');
            }
        });
    }
}
