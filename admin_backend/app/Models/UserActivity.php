<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\UserActivity
 *
 * @property int $id
 * @property int $user_id
 * @property string $model_type
 * @property int $model_id
 * @property int $type
 * @property int $value
 * @property int $ip
 * @property int $device
 * @property int $agent
 * @property int $created_at
 * @method static Builder|Settings newModelQuery()
 * @method static Builder|Settings newQuery()
 * @method static Builder|Settings query()
 * @method static Builder|Settings filter(array $value)
 * @method static Builder|Settings whereId($value)
 * @method static Builder|Settings whereUserId($value)
 * @method static Builder|Settings whereType($value)
 * @method static Builder|Settings whereValue($value)
 * @method static Builder|Settings whereIp($value)
 * @method static Builder|Settings whereDevice($value)
 * @method static Builder|Settings whereAgent($value)
 * @method static Builder|Settings whereCreatedAt($value)
 * @mixin Eloquent
 */
class UserActivity extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    public $casts = [
        'agent' => 'array',
    ];

    public function model(): MorphTo
    {
        return $this->morphTo('model');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeFilter($query, array $filter) {

        $query->when(data_get($filter, 'model_id'), fn($q, $modelId)   => $q->where('model_id', $modelId))
            ->when(data_get($filter, 'model_type'), fn($q, $modelType) => $q->where('model_type', $modelType))
            ->when(data_get($filter, 'user_id'),    fn($q, $userId)    => $q->where('user_id', $userId))
            ->when(data_get($filter, 'type'),       fn($q, $type)      => $q->where('type', $type))
            ->when(data_get($filter, 'value'),      fn($q, $value)     => $q->where('value', $value))
            ->when(data_get($filter, 'ip'),         fn($q, $ip)        => $q->where('ip', $ip))
            ->when(data_get($filter, 'device'),     fn($q, $device)    => $q->where('device', $device))
            ->when(data_get($filter, 'agent'),      fn($q, $agent)     => $q->where('agent', $agent))
            ->when(data_get($filter, 'created_at'), fn($q, $createdAt) => $q->where('created_at', $createdAt))
            ->when(data_get($filter, 'date_from'),  fn($q, $dateFrom)  => $q->where('date', '>=', $dateFrom))
            ->when(data_get($filter, 'date_to'),    fn($q, $dateTo)    => $q->where('date', '<=', $dateTo));
    }
}
