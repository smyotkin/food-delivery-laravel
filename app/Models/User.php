<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\Cache;

use App\Traits\HasRolesAndPermissions;

Date::setLocale('ru');

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions;

    protected $perPage = 100;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'city_id',
        'position_id',
        'email',
        'password',
        'phone',
        'last_seen',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'full_name',
        'phone_formatted',
        'registered_at',
        'online',
    ];

    public function getPhoneFormattedAttribute()
    {
        return $this->phoneNumber($this->phone);
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getRegisteredAtAttribute()
    {
        $date = Date::parse($this->created_at);

        return $date->format(now()->year == $date->year ? 'j F' : 'j F Y');
    }

    public function getOnlineAttribute()
    {
        $timeOrOffline = $this->last_seen != null ? Date::parse($this->last_seen)->diffForHumans() : 'offline';

        return Cache::has('user-is-online-' . $this->id) ? 'online' : $timeOrOffline;
    }

    public function setPhoneAttribute($value)
    {
        $this->attributes['phone'] = $this->toDigit($value);
    }

    public function phoneNumber($number)
    {
        if ($number) {
            $result = sprintf("+%s %s %s-%s-%s",
                substr($number, 0, 1),
                substr($number, 1, 3),
                substr($number, 4, 3),
                substr($number, 7, 2),
                substr($number, 9, 2)
            );
        } else {
            $result = false;
        }

        return $result;
    }

    public static function toDigit($string)
    {
        return str_replace(['+', ' ', '-'], '', $string);
    }
}
