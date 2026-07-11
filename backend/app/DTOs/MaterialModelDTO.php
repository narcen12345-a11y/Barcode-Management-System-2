<?php

namespace App\DTOs;

readonly class MaterialModelDTO
{
    public function __construct(
        public int     $materialTypeId,
        public string  $name,
        public ?string $description,
        public bool    $isActive = true,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            materialTypeId: (int) $data['material_type_id'],
            name: $data['name'],
            description: $data['description'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'material_type_id' => $this->materialTypeId,
            'name' => $this->name,
            'description' => $this->description,
            'is_active' => $this->isActive,
        ];
    }
}
