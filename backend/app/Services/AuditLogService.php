<?php

namespace App\Services;

use App\Interfaces\AuditLogRepositoryInterface;
use Illuminate\Http\Request;

class AuditLogService
{
    public function __construct(
        private readonly AuditLogRepositoryInterface $auditLogRepository,
        private readonly Request $request,
    ) {}

    public function log(
        ?int $userId,
        string $entityType,
        string $entityId,
        string $action,
        ?array $oldValues = null,
        ?array $newValues = null,
    ): void {
        $this->auditLogRepository->create([
            'user_id' => $userId,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
        ]);
    }
}
