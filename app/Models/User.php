<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

use App\Traits\HasRolesAndPermissions;

/**
 * App\Models\User
 *
 * @property int $id
 * @property int $city_id
 * @property int|null $position_id
 * @property string $first_name
 * @property string $last_name
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property string $phone
 * @property string|null $last_seen
 * @property string|null $last_page
 * @property bool $is_active
 * @property-read mixed $full_name
 * @property-read mixed $online
 * @property-read mixed $phone_formatted
 * @property-read mixed $registered_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Permission[] $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\Role[] $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class User extends Authenticatable
{
    use HasFactory;
    use HasRolesAndPermissions;
    use Notifiable;

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'users_permissions'); // ->withDefault()
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'users_roles'); // ->withDefault()
    }

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
        'is_custom_permissions',
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
        'status',
    ];

    public function getStatusAttribute()
    {
        return $this->roles->first()->status ?? null;
    }

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

    public static function isRoot($id = null)
    {
        return !empty($id) ? User::find($id)->id === 1 : Auth::user()->id === 1;
    }

    public static function notRoot()
    {
        return !self::isRoot();
    }
}
