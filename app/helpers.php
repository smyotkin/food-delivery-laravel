<?php

if (!function_exists('has_permission')) {
    function has_permission(string $permission) {
        return Auth::check() && (Auth::user()->hasPermission($permission) || Auth::user()->isRoot());
    }
}
