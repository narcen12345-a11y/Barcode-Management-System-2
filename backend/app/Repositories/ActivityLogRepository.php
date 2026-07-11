<?php

namespace App\Repositories;

use App\Interfaces\ActivityLogRepositoryInterface;
use App\Models\ActivityLog;

class ActivityLogRepository implements ActivityLogRepositoryInterface
{
    public function create(array $data): ActivityLog
    {
        return ActivityLog::create($data);
    }
}
