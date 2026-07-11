<?php

namespace App\Enums;

enum BarcodeHistoryTypeEnum: string
{
    case CREATE = 'CREATE';
    case UPDATE = 'UPDATE';
    case STATUS_CHANGE = 'STATUS_CHANGE';
    case RESTORE = 'RESTORE';
    case SOFT_DELETE = 'SOFT_DELETE';
}
