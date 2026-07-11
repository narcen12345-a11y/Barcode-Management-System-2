<?php

namespace App\Services;

use App\DTOs\BarcodeDTO;
use App\DTOs\UpdateBarcodeDTO;
use App\Enums\BarcodeHistoryTypeEnum;
use App\Enums\BarcodeStatusEnum;
use App\Interfaces\BarcodeRepositoryInterface;
use App\Models\Barcode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BarcodeService
{
    public function __construct(
        private readonly BarcodeRepositoryInterface $barcodeRepository,
        private readonly BarcodeHistoryService $barcodeHistoryService,
        private readonly AuditLogService $auditLogService,
        private readonly ActivityLogService $activityLogService,
    ) {}

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->barcodeRepository->findAllPaginated($filters, $perPage);
    }

    public function findAll(): Collection
    {
        return $this->barcodeRepository->findAll();
    }

    public function findById(int $id): ?Barcode
    {
        return $this->barcodeRepository->findById($id);
    }

    public function findByBarcodeId(string $barcodeId): ?Barcode
    {
        return $this->barcodeRepository->findByBarcodeId($barcodeId);
    }

    public function generateBarcodeId(): string
    {
        $datePrefix = now()->format('Ymd');
        $nextSequence = $this->barcodeRepository->getNextBarcodeSequence($datePrefix);

        return sprintf('BRC-%s-%03d', $datePrefix, $nextSequence);
    }

    public function create(array $data): Barcode
    {
        return DB::transaction(function () use ($data) {
            $barcodeId = $this->generateBarcodeId();
            $userId = auth()->id();

            $dto = BarcodeDTO::fromRequest($data, $barcodeId, $userId);
            $barcode = $this->barcodeRepository->create($dto->toArray());

            // Create Barcode History: CREATE
            $this->barcodeHistoryService->create([
                'barcode_id' => $barcode->id,
                'field_name' => 'barcode',
                'old_value' => null,
                'new_value' => json_encode($barcode->toArray()),
                'changed_by' => $userId,
                'change_reason' => 'Barcode baru dibuat',
            ]);

            // Audit Log
            $this->auditLogService->log(
                userId: $userId,
                entityType: 'barcode',
                entityId: (string) $barcode->id,
                action: 'create_barcode',
                oldValues: null,
                newValues: $barcode->toArray(),
            );

            // Activity Log
            $this->activityLogService->log(
                userId: $userId,
                activity: 'create_barcode',
                module: 'Barcode',
                description: "Barcode {$barcode->barcode_id} berhasil dibuat.",
            );

            return $barcode->load(['material.materialType', 'material.materialModel', 'site', 'createdBy', 'updatedBy']);
        });
    }

    public function update(int $id, array $data): Barcode
    {
        return DB::transaction(function () use ($id, $data) {
            $barcode = $this->barcodeRepository->findById($id);

            if (!$barcode) {
                throw new NotFoundHttpException('Barcode tidak ditemukan.');
            }

            $oldValues = $barcode->toArray();
            $userId = auth()->id();

            $dto = UpdateBarcodeDTO::fromRequest($data, $userId);
            $barcode = $this->barcodeRepository->update($barcode, $dto->toArray());

            // Track changes for history
            $changes = $this->getChanges($oldValues, $barcode->toArray());

            foreach ($changes as $field => $change) {
                $changeType = $field === 'status' ? BarcodeHistoryTypeEnum::STATUS_CHANGE->value : BarcodeHistoryTypeEnum::UPDATE->value;

                $this->barcodeHistoryService->create([
                    'barcode_id' => $barcode->id,
                    'field_name' => $field,
                    'old_value' => $change['old'],
                    'new_value' => $change['new'],
                    'changed_by' => $userId,
                    'change_reason' => $change['reason'],
                ]);
            }

            // Audit Log
            $this->auditLogService->log(
                userId: $userId,
                entityType: 'barcode',
                entityId: (string) $barcode->id,
                action: 'update_barcode',
                oldValues: $oldValues,
                newValues: $barcode->toArray(),
            );

            // Activity Log
            $this->activityLogService->log(
                userId: $userId,
                activity: 'update_barcode',
                module: 'Barcode',
                description: "Barcode {$barcode->barcode_id} berhasil diperbarui.",
            );

            return $barcode;
        });
    }

    public function delete(int $id): void
    {
        DB::transaction(function () use ($id) {
            $barcode = $this->barcodeRepository->findById($id);

            if (!$barcode) {
                throw new NotFoundHttpException('Barcode tidak ditemukan.');
            }

            $oldValues = $barcode->toArray();
            $userId = auth()->id();

            $this->barcodeRepository->delete($barcode);

            // Create Barcode History: SOFT_DELETE
            $this->barcodeHistoryService->create([
                'barcode_id' => $barcode->id,
                'field_name' => 'deleted_at',
                'old_value' => null,
                'new_value' => now()->toISOString(),
                'changed_by' => $userId,
                'change_reason' => 'Barcode dihapus (soft delete)',
            ]);

            // Audit Log
            $this->auditLogService->log(
                userId: $userId,
                entityType: 'barcode',
                entityId: (string) $id,
                action: 'delete_barcode',
                oldValues: $oldValues,
                newValues: null,
            );

            // Activity Log
            $this->activityLogService->log(
                userId: $userId,
                activity: 'delete_barcode',
                module: 'Barcode',
                description: "Barcode {$barcode->barcode_id} berhasil dihapus.",
            );
        });
    }

    public function restore(int $id): void
    {
        DB::transaction(function () use ($id) {
            $barcode = $this->barcodeRepository->findById($id);

            if (!$barcode) {
                $trashedBarcode = $this->barcodeRepository->findOnlyTrashed($id);
                if ($trashedBarcode) {
                    $userId = auth()->id();

                    $this->barcodeRepository->restore($trashedBarcode);

                    // Create Barcode History: RESTORE
                    $this->barcodeHistoryService->create([
                        'barcode_id' => $trashedBarcode->id,
                        'field_name' => 'deleted_at',
                        'old_value' => $trashedBarcode->deleted_at?->toISOString(),
                        'new_value' => null,
                        'changed_by' => $userId,
                        'change_reason' => 'Barcode dipulihkan',
                    ]);

                    // Audit Log
                    $this->auditLogService->log(
                        userId: $userId,
                        entityType: 'barcode',
                        entityId: (string) $id,
                        action: 'restore_barcode',
                        oldValues: null,
                        newValues: $trashedBarcode->fresh()->toArray(),
                    );

                    // Activity Log
                    $this->activityLogService->log(
                        userId: $userId,
                        activity: 'restore_barcode',
                        module: 'Barcode',
                        description: "Barcode {$trashedBarcode->barcode_id} berhasil dipulihkan.",
                    );

                    return;
                }
                throw new NotFoundHttpException('Barcode tidak ditemukan.');
            }
        });
    }

    private function getChanges(array $oldValues, array $newValues): array
    {
        $changes = [];
        $trackedFields = ['material_id', 'site_id', 'serial_number', 'status', 'description'];

        foreach ($trackedFields as $field) {
            $old = $oldValues[$field] ?? null;
            $new = $newValues[$field] ?? null;

            if ($old != $new) {
                $reason = match ($field) {
                    'material_id' => 'Mengubah Material',
                    'site_id' => 'Mengubah Site',
                    'serial_number' => 'Mengubah Serial Number',
                    'status' => 'Mengubah Status',
                    'description' => 'Mengubah Deskripsi',
                    default => "Mengubah {$field}",
                };

                $changes[$field] = [
                    'old' => is_array($old) ? json_encode($old) : (string) $old,
                    'new' => is_array($new) ? json_encode($new) : (string) $new,
                    'reason' => $reason,
                ];
            }
        }

        return $changes;
    }
}
