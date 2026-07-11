<?php

namespace App\Services;

use App\Interfaces\ActivityLogRepositoryInterface;
use Illuminate\Http\Request;

class ActivityLogService
{
    public function __construct(
        private readonly ActivityLogRepositoryInterface $activityLogRepository,
        private readonly Request $request,
    ) {}

    public function log(
        ?int $userId,
        string $activity,
        string $module,
        ?string $description = null,
        ?string $sessionId = null,
    ): void {
        $this->activityLogRepository->create([
            'user_id' => $userId,
            'activity' => $activity,
            'module' => $module,
            'description' => $description,
            'ip_address' => $this->request->ip(),
            'user_agent' => $this->request->userAgent(),
            'session_id' => $sessionId ?? session()->getId(),
        ]);
    }
}
