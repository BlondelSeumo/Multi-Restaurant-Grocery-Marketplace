<?php

namespace App\Models;

use Database\Factories\SmsGatewayFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\SmsGateway
 *
 * @property int $id
 * @property string $title
 * @property string $from
 * @property string $type
 * @property string|null $api_key
 * @property string|null $secret_key
 * @property string|null $service_id
 * @property string|null $text
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static SmsGatewayFactory factory(...$parameters)
 * @method static Builder|SmsGateway newModelQuery()
 * @method static Builder|SmsGateway newQuery()
 * @method static Builder|SmsGateway query()
 * @method static Builder|SmsGateway whereActive($value)
 * @method static Builder|SmsGateway whereApiKey($value)
 * @method static Builder|SmsGateway whereCreatedAt($value)
 * @method static Builder|SmsGateway whereFrom($value)
 * @method static Builder|SmsGateway whereId($value)
 * @method static Builder|SmsGateway whereSecretKey($value)
 * @method static Builder|SmsGateway whereServiceId($value)
 * @method static Builder|SmsGateway whereText($value)
 * @method static Builder|SmsGateway whereTitle($value)
 * @method static Builder|SmsGateway whereType($value)
 * @method static Builder|SmsGateway whereUpdatedAt($value)
 * @mixin Eloquent
 */
class SmsGateway extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    protected $hidden = ['created_at', 'updated_at'];

}
