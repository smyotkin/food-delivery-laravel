<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Modules extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'url',
        'is_control',
        'is_active',
    ];

    protected static $subModulesPermissions = [
        'users' => [
            'users_view' => '/users',
            'users_positions_view' => '/users/positions',
        ],
        'settings' => [
            'settings_view' => '/settings',
            'events_modify_and_view' => '/system/events',
            'log_modify_and_view' => '/system/logs',
            'notifications_modify_and_view' => '/system/notifications',
        ]
    ];

    /**
     * Возвращает только доступные авторизованному пользователю модули (если есть права на просмотр и если если есть
     * право на просмотр подмодуля)
     *
     * @return mixed
     */
    public static function getAvailable()
    {
        $authUserPermissions = Auth::user()->permissions()->get();

        $getModulesViewRules = !empty($authUserPermissions) ? $authUserPermissions->filter(function ($item) {
            return preg_match("/(.*)_view/m", $item->slug);
        })->pluck('slug')->toArray() : [];

        $getActiveModules = self::where('is_active', 1)->get();
        $availableModules = collect([]);

        foreach ($getActiveModules as $module) {
            if (!empty(self::$subModulesPermissions[$module['slug']])) {
                foreach (self::$subModulesPermissions[$module['slug']] as $permission => $url) {
                    if (in_array($permission, $getModulesViewRules)) {
                        $module->url = $url;
                        $availableModules[] = $module;

                        continue 2;
                    }
                }
            }
        }

        return $availableModules;
    }

    /**
     * Фильтрует доступные модули по ключу и значению (допустим вернуть только те, у которых is_control == 1)
     *
     * @param $key
     * @param $value
     * @return mixed
     */
    public static function getFilteredAvailable($key, $value)
    {
        return Modules::getAvailable()->filter(function ($item) use ($key, $value) {
            return $item->$key == $value;
        });
    }
}
