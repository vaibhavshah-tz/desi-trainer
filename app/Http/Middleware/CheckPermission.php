<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use PermissionHelper;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, ...$permission)
    {
        if (empty($permission)) {
            $permission = [null];
        }

        if(PermissionHelper::hasAccess($permission)) {
            return $next($request);
        }

        throw new AccessDeniedHttpException();
    }
}
