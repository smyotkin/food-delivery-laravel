<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static \Database\Factories\UserFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin query()
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereCityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereLastPage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereLastSeen($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin wherePhone($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin wherePositionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|SentPin whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SentPin extends Model
{
    use HasFactory;

    protected $table = 'sent_pin';

    public const UPDATED_AT = null;

    protected $fillable = [
        'phone',
        'pin_code',
        'created_at',
        'is_active',
    ];
}
