<?php

namespace App\Interfaces;

use App\Models\Barcode;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface BarcodeRepositoryInterface
{
    public function findById(int $id): ?Barcode;

    public function findByBarcodeId(string $barcodeId): ?Barcode;

    public function findBySerialNumber(string $serialNumber): ?Barcode;

    public function findAllPaginated(array $filters = [], int $perPage = 10): LengthAwarePaginator;

    public function findAll(): Collection;

    public function create(array $data): Barcode;

    public function update(Barcode $barcode, array $data): Barcode;

    public function delete(Barcode $barcode): void;

    public function restore(Barcode $barcode): void;

    public function findOnlyTrashed(int $id): ?Barcode;

    public function getNextBarcodeSequence(string $datePrefix): int;
}
