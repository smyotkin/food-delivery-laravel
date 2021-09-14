<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventsNotifications extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';

    public $incrementing = false;

    protected $fillable = [
        'msg_template',
        'recipient_ids',
    ];

    public static function get($key)
    {
        return self::where('key', $key)->first();
    }

    public static function getOrFail($key)
    {
        return self::where('key', $key)->firstOrFail();
    }
}
