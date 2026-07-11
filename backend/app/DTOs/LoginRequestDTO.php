<?php

namespace App\DTOs;

readonly class LoginRequestDTO
{
    public function __construct(
        public string $login,
        public string $password,
        public bool   $remember = false,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            login: $data['login'],
            password: $data['password'],
            remember: (bool) ($data['remember'] ?? false),
        );
    }
}
