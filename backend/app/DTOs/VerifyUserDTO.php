<?php

namespace App\DTOs;

readonly class VerifyUserDTO
{
    public function __construct(
        public int    $userId,
        public string $status,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            userId: (int) $data['user_id'],
            status: $data['status'],
        );
    }
}
