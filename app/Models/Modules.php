<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Modules extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'url',
        'is_control',
        'is_active',
    ];

    /**
     * Возвращает только доступные авторизованному пользователю модули (если есть права на просмотр)
     *
     * @param null $user
     * @return mixed
     */
    public static function getAvailable($user = null)
    {
        $authUserPermissions = Auth::user()->permissions()->get();

        $getModulesViewRules = !empty($authUserPermissions) ? $authUserPermissions->filter(function ($item) {
            return substr_count($item->slug, '_') == 1 && preg_match("/(.*)\_view/m", $item->slug);
        })->pluck('slug')->map(function ($item) {
            return str_replace('_view', '', $item);
        })->toArray() : [];

        return self::where('is_active', 1)->whereIn('slug', $getModulesViewRules)->get();
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
