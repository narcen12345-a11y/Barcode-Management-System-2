<?php

namespace App\DTOs;

readonly class ChangePasswordDTO
{
    public function __construct(
        public int    $userId,
        public string $currentPassword,
        public string $newPassword,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            userId: (int) ($data['user_id'] ?? auth()->id()),
            currentPassword: $data['current_password'],
            newPassword: $data['new_password'],
        );
    }
}
