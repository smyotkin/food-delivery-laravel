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
        'user',
    ];

    protected $fillable = [
        'slug',
        'label',
        'msg',
        'data',
        'user_id',
        'created_at',
    ];

    public const UPDATED_AT = null;

    public function getDateAttribute()
    {
        $date = Date::parse($this->created_at);

        return $date->format(now()->year == $date->year ? 'j F, H:i' : 'j F Y, H:i');
    }

    public function getUserAttribute()
    {
        return User::find($this->user_id) ?? false;
    }

    public function getDataFormattedAttribute()
    {
        return json_encode(json_decode($this->data, true), JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
    }
}
