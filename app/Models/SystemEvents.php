<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;

class SystemEvents extends Model
{
    use HasFactory;

    protected $appends = [
        'date',
        'user'
    ];

    public function getDateAttribute()
    {
        $date = Date::parse($this->created_at);

        return $date->format(now()->year == $date->year ? 'j F, H:i' : 'j F Y');
    }

    public function getUserAttribute()
    {
        return User::find($this->user_id) ?? false;
    }
}
