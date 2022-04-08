<?php

namespace App\Helpers;

use App\Models\Role;
use Arr;
use Illuminate\Support\Facades\Cache;

class PermissionHelper
{
    /**
     * Get user's permissions
     * 
     * @param string $key
     * @return array
     */
    public static function getPermission($key = null)
    {
        if(!empty($key)) {
            $permissions = Cache::remember($key, config('constants.ADMIN_PERMISSION.CACHE_EXPIRED_TIME'),function () {
                $role = Role::findOrFail(\Auth::user()->role_id);

                return serialize(array_column($role->permissions->toArray(), 'name'));
            });

            return unserialize($permissions);
        }
    }

    /**
     * Remove cache
     * 
     * @param string $key
     */
    public static function removeCache($key = null)
    {
        if(Cache::has($key)) {
            Cache::forget($key);
        }
    }

    /**
     * Check if user has access to particular page or interface 
     * 
     * @param array|string $permissions
     * @return boolean
     */
    public static function hasAccess($permissions = null)
    {
        $userPermissions = [];
        if(\Auth::user()->role_id == config('constants.ADMIN_ROLE.SUPER_ADMIN')) {
            return true;
        }

        if(\Auth::check()) {
            $userPermissions = self::getPermission(config('constants.ADMIN_PERMISSION.PERMISSION_KEY').\Auth::id());
        }

        if(empty($permissions) || empty($userPermissions)) {
            return false;
        }
        // echo '<pre>';print_r($permissions);print_r($userPermissions);
        // dd(Arr::has($userPermissions, $permissions));exit;
        if(is_array($permissions) && !empty(array_intersect($userPermissions, $permissions))){
            return true;
        }

        if(in_array($permissions, $userPermissions)) {
            return true;
        }

        return false;
    }
}
