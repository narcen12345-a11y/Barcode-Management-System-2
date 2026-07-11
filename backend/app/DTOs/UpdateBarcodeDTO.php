<?php

namespace App\DTOs;

readonly class UpdateBarcodeDTO
{
    public function __construct(
        public ?int    $materialId,
        public ?int    $siteId,
        public ?string $serialNumber,
        public ?string $status,
        public ?string $description,
        public ?int    $updatedBy,
    ) {}

    public static function fromRequest(array $data, ?int $userId): self
    {
        return new self(
            materialId: isset($data['material_id']) ? (int) $data['material_id'] : null,
            siteId: isset($data['site_id']) ? (int) $data['site_id'] : null,
            serialNumber: $data['serial_number'] ?? null,
            status: $data['status'] ?? null,
            description: $data['description'] ?? null,
            updatedBy: $userId,
        );
    }

    public function toArray(): array
    {
        $result = [];

        if ($this->materialId !== null) {
            $result['material_id'] = $this->materialId;
        }
        if ($this->siteId !== null) {
            $result['site_id'] = $this->siteId;
        }
        if ($this->serialNumber !== null) {
            $result['serial_number'] = $this->serialNumber;
        }
        if ($this->status !== null) {
            $result['status'] = $this->status;
        }
        if ($this->description !== null) {
            $result['description'] = $this->description;
        }
        if ($this->updatedBy !== null) {
            $result['updated_by'] = $this->updatedBy;
        }

        return $result;
    }
}
