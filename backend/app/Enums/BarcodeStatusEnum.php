<?php

namespace App\Enums;

enum BarcodeStatusEnum: string
{
    case NEW = 'NEW';
    case OLD = 'OLD';

    public function label(): string
    {
        return match ($this) {
            self::NEW => 'NEW (MOS)',
            self::OLD => 'OLD (DISMANTLE)',
        };
    }
}
