<?php

namespace App\DTOs;

readonly class RegisterUserDTO
{
    public function __construct(
        public string $username,
        public string $email,
        public string $password,
        public string $fullName,
        public ?array $roleIds = null,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            username: $data['username'],
            email: $data['email'],
            password: $data['password'],
            fullName: $data['full_name'],
            roleIds: $data['role_ids'] ?? null,
        );
    }
}
