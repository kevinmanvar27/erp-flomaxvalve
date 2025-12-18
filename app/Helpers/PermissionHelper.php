<?php

namespace App\Helpers;

class PermissionHelper
{
    public static function hasPermission($resource, $action)
    {
        // Get user permissions from the session
        $permissions = session('user_permissions', []);

        // Check if the user has the required permission
        return isset($permissions[$resource]) && $permissions[$resource][$action] == '1';
    }
}
