<?php

namespace App\DTOs;

readonly class SiteDTO
{
    public function __construct(
        public string  $siteId,
        public string  $siteName,
        public ?string $region,
        public ?string $address,
        public ?string $latitude,
        public ?string $longitude,
        public bool    $isActive = true,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            siteId: $data['site_id'],
            siteName: $data['site_name'],
            region: $data['region'] ?? null,
            address: $data['address'] ?? null,
            latitude: $data['latitude'] ?? null,
            longitude: $data['longitude'] ?? null,
            isActive: (bool) ($data['is_active'] ?? true),
        );
    }

    public function toArray(): array
    {
        return [
            'site_id' => $this->siteId,
            'site_name' => $this->siteName,
            'region' => $this->region,
            'address' => $this->address,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->isActive,
        ];
    }
}
