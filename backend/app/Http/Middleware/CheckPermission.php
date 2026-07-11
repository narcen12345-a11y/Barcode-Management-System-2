<?php

namespace App\Http\Middleware;

use App\Enums\PermissionEnum;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = $request->user();

        if (!$user) {
            throw new UnauthorizedHttpException('', 'Unauthenticated.');
        }

        // Super Admin has all permissions
        if ($user->isSuperAdmin()) {
            return $next($request);
        }

        if (!$user->hasPermission($permission)) {
            throw new AccessDeniedHttpException('Akses ditolak.');
        }

        return $next($request);
    }
}
