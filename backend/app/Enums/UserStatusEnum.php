<?php

namespace App\Enums;

enum UserStatusEnum: string
{
    case PENDING_VERIFICATION = 'pending_verification';
    case ACTIVE = 'active';
    case INACTIVE = 'inactive';
    case SUSPENDED = 'suspended';

    public function label(): string
    {
        return match ($this) {
            self::PENDING_VERIFICATION => 'Pending Verification',
            self::ACTIVE => 'Active',
            self::INACTIVE => 'Inactive',
            self::SUSPENDED => 'Suspended',
        };
    }

    public function canLogin(): bool
    {
        return $this === self::ACTIVE;
    }
}
