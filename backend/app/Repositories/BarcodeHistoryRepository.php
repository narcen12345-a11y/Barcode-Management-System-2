<?php

namespace App\Repositories;

use App\Interfaces\BarcodeHistoryRepositoryInterface;
use App\Models\BarcodeHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BarcodeHistoryRepository implements BarcodeHistoryRepositoryInterface
{
    public function findById(int $id): ?BarcodeHistory
    {
        return BarcodeHistory::with(['changedBy'])->find($id);
    }

    public function findByBarcodeId(int $barcodeId): Collection
    {
        return BarcodeHistory::with(['changedBy'])
            ->where('barcode_id', $barcodeId)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function findAllPaginatedByBarcode(int $barcodeId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = BarcodeHistory::with(['changedBy'])
            ->where('barcode_id', $barcodeId);

        if (!empty($filters['field_name'])) {
            $query->where('field_name', $filters['field_name']);
        }

        if (!empty($filters['changed_by'])) {
            $query->where('changed_by', $filters['changed_by']);
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($perPage);
    }

    public function create(array $data): BarcodeHistory
    {
        return BarcodeHistory::create($data);
    }
}
