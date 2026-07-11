<?php

namespace App\DTOs;

readonly class MaterialDTO
{
    public function __construct(
        public int     $materialTypeId,
        public int     $materialModelId,
        public string  $materialCode,
        public string  $name,
        public ?string $description,
        public bool    $isActive = true,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            materialTypeId: (int) $data['material_type_id'],
            materialModelId: (int) $data['material_model_id'],
            materialCode: $data['material_code'],
            name: $data['name'],
            description: $data['description'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'material_type_id' => $this->materialTypeId,
            'material_model_id' => $this->materialModelId,
            'material_code' => $this->materialCode,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
