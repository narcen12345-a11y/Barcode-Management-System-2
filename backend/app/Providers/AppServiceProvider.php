<?php

namespace App\Providers;

use App\Interfaces\ActivityLogRepositoryInterface;
use App\Interfaces\AuditLogRepositoryInterface;
use App\Interfaces\BarcodeHistoryRepositoryInterface;
use App\Interfaces\BarcodeRepositoryInterface;
use App\Interfaces\MaterialModelRepositoryInterface;
use App\Interfaces\MaterialRepositoryInterface;
use App\Interfaces\MaterialTypeRepositoryInterface;
use App\Interfaces\PermissionRepositoryInterface;
use App\Interfaces\RoleRepositoryInterface;
use App\Interfaces\SiteRepositoryInterface;
use App\Interfaces\UserRepositoryInterface;
use App\Repositories\ActivityLogRepository;
use App\Repositories\AuditLogRepository;
use App\Repositories\BarcodeHistoryRepository;
use App\Repositories\BarcodeRepository;
use App\Repositories\MaterialModelRepository;
use App\Repositories\MaterialRepository;
use App\Repositories\MaterialTypeRepository;
use App\Repositories\PermissionRepository;
use App\Repositories\RoleRepository;
use App\Repositories\SiteRepository;
use App\Repositories\UserRepository;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRepositoryInterface::class, UserRepository::class);
        $this->app->bind(RoleRepositoryInterface::class, RoleRepository::class);
        $this->app->bind(PermissionRepositoryInterface::class, PermissionRepository::class);
        $this->app->bind(AuditLogRepositoryInterface::class, AuditLogRepository::class);
        $this->app->bind(ActivityLogRepositoryInterface::class, ActivityLogRepository::class);
        $this->app->bind(SiteRepositoryInterface::class, SiteRepository::class);
        $this->app->bind(MaterialTypeRepositoryInterface::class, MaterialTypeRepository::class);
        $this->app->bind(MaterialModelRepositoryInterface::class, MaterialModelRepository::class);
        $this->app->bind(MaterialRepositoryInterface::class, MaterialRepository::class);
        $this->app->bind(BarcodeRepositoryInterface::class, BarcodeRepository::class);
        $this->app->bind(BarcodeHistoryRepositoryInterface::class, BarcodeHistoryRepository::class);
    }

    public function boot(): void
    {
        //
    }
}
