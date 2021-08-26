<?php

if (!function_exists('has_permission'))
{
    function has_permission(string $permission)
    {
        return Auth::check() && (Auth::user()->hasPermission($permission) || Auth::user()->isRoot());
    }
}

if (!function_exists('mb_ucfirst'))
{
    function mb_ucfirst($text)
    {
        return mb_strtoupper(mb_substr($text, 0, 1)) . mb_substr($text, 1);
    }
}
