<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Settings extends Model
{
    use HasFactory;

    protected $primaryKey = 'key';

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = [
        'value',
    ];

    public static function get($key)
    {
        return self::where('key', $key)->first()->value;
    }

    public static function getDecrypted($key)
    {
        $value = self::where('key', $key)->first()->value;

        return $value ? Crypt::decryptString($value) : false;
    }
}
