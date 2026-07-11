<?php

namespace App\Repositories;

use App\Interfaces\AuditLogRepositoryInterface;
use App\Models\AuditLog;

class AuditLogRepository implements AuditLogRepositoryInterface
{
    public function create(array $data): AuditLog
    {
        return AuditLog::create($data);
    }
}
