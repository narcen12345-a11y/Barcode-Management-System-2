<?php

namespace App\Services;

use App\Interfaces\BarcodeHistoryRepositoryInterface;
use App\Models\BarcodeHistory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class BarcodeHistoryService
{
    public function __construct(
        private readonly BarcodeHistoryRepositoryInterface $barcodeHistoryRepository,
    ) {}

    public function findByBarcodeId(int $barcodeId): Collection
    {
        return $this->barcodeHistoryRepository->findByBarcodeId($barcodeId);
    }

    public function findAllPaginatedByBarcode(int $barcodeId, array $filters = [], int $perPage = 10): LengthAwarePaginator
    {
        return $this->barcodeHistoryRepository->findAllPaginatedByBarcode($barcodeId, $filters, $perPage);
    }

    public function create(array $data): BarcodeHistory
    {
        return $this->barcodeHistoryRepository->create($data);
    }
}
