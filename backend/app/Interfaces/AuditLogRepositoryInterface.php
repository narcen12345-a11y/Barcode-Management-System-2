<?php

namespace App\Interfaces;

use App\Models\AuditLog;

interface AuditLogRepositoryInterface
{
    public function create(array $data): AuditLog;
}
