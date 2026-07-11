<?php

namespace App\Interfaces;

use App\Models\BarcodeHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BarcodeHistoryRepositoryInterface
{
    public function findById(int $id): ?BarcodeHistory;

    public function findByBarcodeId(int $barcodeId): Collection;

    public function findAllPaginatedByBarcode(int $barcodeId, array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function create(array $data): BarcodeHistory;
}
