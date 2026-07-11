<?php

namespace App\DTOs;

readonly class BarcodeDTO
{
    public function __construct(
        public string  $barcodeId,
        public int     $materialId,
        public int     $siteId,
        public string  $serialNumber,
        public string  $status,
        public ?string $description,
        public ?int    $createdBy,
    ) {}

    public static function fromRequest(array $data, string $barcodeId, ?int $userId): self
    {
        return new self(
            barcodeId: $barcodeId,
            materialId: (int) $data['material_id'],
            siteId: (int) $data['site_id'],
            serialNumber: $data['serial_number'],
            status: $data['status'],
            description: $data['description'] ?? null,
            createdBy: $userId,
        );
    }

    public function toArray(): array
    {
        return [
            'barcode_id' => $this->barcodeId,
            'material_id' => $this->materialId,
            'site_id' => $this->siteId,
            'serial_number' => $this->serialNumber,
            'status' => $this->status,
            'description' => $this->description,
            'is_active' => true,
            'created_by' => $this->createdBy,
        ];
    }
}
