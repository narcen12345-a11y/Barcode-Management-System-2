<?php

namespace App\Repositories;

use App\Interfaces\BarcodeRepositoryInterface;
use App\Models\Barcode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class BarcodeRepository implements BarcodeRepositoryInterface
{
    public function findById(int $id): ?Barcode
    {
        return Barcode::with(['material.materialType', 'material.materialModel', 'site', 'createdBy', 'updatedBy'])->find($id);
    }

    public function findByBarcodeId(string $barcodeId): ?Barcode
    {
        return Barcode::with(['material.materialType', 'material.materialModel', 'site', 'createdBy', 'updatedBy'])
            ->where('barcode_id', $barcodeId)
            ->first();
    }

    public function findBySerialNumber(string $serialNumber): ?Barcode
    {
        return Barcode::where('serial_number', $serialNumber)->first();
    }

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Barcode::with(['material.materialType', 'material.materialModel', 'site', 'createdBy', 'updatedBy']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('barcode_id', 'like', "%{$search}%")
                    ->orWhere('serial_number', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['barcode_id'])) {
            $query->where('barcode_id', 'like', "%{$filters['barcode_id']}%");
        }

        if (!empty($filters['serial_number'])) {
            $query->where('serial_number', 'like', "%{$filters['serial_number']}%");
        }

        if (!empty($filters['site_id'])) {
            $query->where('site_id', $filters['site_id']);
        }

        if (!empty($filters['material_id'])) {
            $query->where('material_id', $filters['material_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['is_active'])) {
            $query->where('is_active', $filters['is_active']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
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
        return Barcode::with(['material.materialType', 'material.materialModel', 'site', 'createdBy', 'updatedBy'])->get();
    }

    public function create(array $data): Barcode
    {
        return Barcode::create($data);
    }

    public function update(Barcode $barcode, array $data): Barcode
    {
        $barcode->update($data);
        return $barcode->fresh()->load(['material.materialType', 'material.materialModel', 'site', 'createdBy', 'updatedBy']);
    }

    public function delete(Barcode $barcode): void
    {
        $barcode->delete();
    }

    public function restore(Barcode $barcode): void
    {
        $barcode->restore();
    }

    public function findOnlyTrashed(int $id): ?Barcode
    {
        return Barcode::onlyTrashed()->find($id);
    }

    public function getNextBarcodeSequence(string $datePrefix): int
    {
        $lastBarcode = Barcode::where('barcode_id', 'like', "{$datePrefix}-%")
            ->orderBy('barcode_id', 'desc')
            ->first();

        if (!$lastBarcode) {
            return 1;
        }

        $parts = explode('-', $lastBarcode->barcode_id);
        $lastSequence = (int) end($parts);

        return $lastSequence + 1;
    }
}
