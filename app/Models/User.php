<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

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
        'email_verified_at' => 'datetime',
    ];

    public function phoneNumber($number) {
        $result = sprintf("+%s %s %s-%s-%s",
            substr($number, 0, 1),
            substr($number, 1, 3),
            substr($number, 4, 3),
            substr($number, 7, 2),
            substr($number, 9, 2)
        );

        return $result;
    }
}
