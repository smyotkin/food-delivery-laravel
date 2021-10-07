<?php

namespace App\Models;

use App\Services\UsersService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ivanov\Helpers\TimeHelper;

/**
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder query()
 * @method static \Illuminate\Database\Eloquent\Builder whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereLastPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Cities extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'phone',
        'folder',
        'timezone',
        'work_hours',
        'work_hours_shift',
        'is_active',
    ];

    protected $appends = [
        'timezone_formatted',
        'is_now_open',
        'working_time_today',
        'multicode',
        'phone_code',
    ];

    public function getFolderAttribute()
    {
        return $this->attributes['folder'] == '/' ? '' : $this->attributes['folder'];
    }

    public function getTimezoneFormattedAttribute()
    {
        return UsersService::timezones[$this->timezone] ?? 'Не найден';
    }

    public function getWorkHoursArrayAttribute()
    {
        return json_decode($this->work_hours, true);
    }

    public function getMulticodeAttribute()
    {
        return $this->settings->where('name', '=', 'multicode')->first()->value ?? null;
    }

    public function getPhoneCodeAttribute()
    {
        return $this->settings->where('name', '=', 'phone_code')->first()->value ?? null;
    }

    public function getWorkingTimeTodayAttribute()
    {
        return TimeHelper::getWorkingTimeToday($this->work_hours_array, $this->timezone, $this->work_hours_shift);
    }

    public function getIsNowOpenAttribute()
    {
        return TimeHelper::IsNowOpen($this->work_hours_array, $this->timezone, $this->work_hours_shift);
    }

    public function settings()
    {
        return $this->hasMany(CitiesSettings::class, 'city_id');
    }
}
