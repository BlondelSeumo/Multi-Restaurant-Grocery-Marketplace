<?php

namespace App\Models;

use Database\Factories\SmsPayloadFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\SmsPayload
 *
 * @property string|null $type
 * @property array|null $payload
 * @property boolean|null $default
 * @property string|null $deleted_at
 * @method static SmsPayloadFactory factory(...$parameters)
 * @method static Builder|SmsPayload newModelQuery()
 * @method static Builder|SmsPayload newQuery()
 * @method static Builder|SmsPayload query()
 * @method static Builder|SmsPayload whereDeletedAt($value)
 * @method static Builder|SmsPayload whereId($value)
 * @mixin Eloquent
 */
class SmsPayload extends Model
{
    use HasFactory, SoftDeletes;

    public $primaryKey      = 'type';
    public $incrementing    = false;
    public $timestamps      = false;
    protected $guarded      = [];
    protected $casts        = [
        'payload' => 'array',
        'default' => 'boolean'
    ];

    const FIREBASE  = 'firebase';
    const TWILIO    = 'twilio';

    const TYPES = [
        self::TWILIO,
        self::FIREBASE
    ];

}
