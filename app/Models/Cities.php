<?php

namespace App\Models;

use App\Services\UsersService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'phone_formatted',
        'timezone_formatted',
    ];

    public function getPhoneFormattedAttribute()
    {
        return "+7 {$this->phone}";
    }

    public function getTimezoneFormattedAttribute()
    {
        return UsersService::timezones[$this->timezone] ?? 'Не найден';
    }

    public function getWorkHoursArrayAttribute()
    {
        return json_decode($this->work_hours, true);
    }

    public function settings()
    {
        return $this->belongsToMany(CitiesSettings::class, 'cities_settings');
    }
}
