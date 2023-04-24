<?php

namespace App\Models;

use App\Traits\SetCurrency;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Bonus
 *
 * @property int $id
 * @property string|null $bonusable_type
 * @property int|null $bonusable_id
 * @property int $bonus_quantity
 * @property int|null $bonus_stock_id
 * @property int|null $value
 * @property int|null $rate_value
 * @property string|null $type
 * @property Carbon|null $expired_at
 * @property int $status
 * @property int $shop_id
 * @property Shop $shop
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Shop|Product|null $bonusable
 * @property-read Stock|null $stock
 * @method static Builder|Bonus active()
 * @method static Builder|Bonus filter(array $filter)
 * @method static Builder|Bonus newModelQuery()
 * @method static Builder|Bonus newQuery()
 * @method static Builder|Bonus query()
 * @method static Builder|Bonus whereStocksId($value)
 * @method static Builder|Bonus whereBonusQuantity($value)
 * @method static Builder|Bonus whereCreatedAt($value)
 * @method static Builder|Bonus whereExpiredAt($value)
 * @method static Builder|Bonus whereId($value)
 * @method static Builder|Bonus whereOrderAmount($value)
 * @method static Builder|Bonus whereShopId($value)
 * @method static Builder|Bonus whereStatus($value)
 * @method static Builder|Bonus whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Bonus extends Model
{
    use HasFactory, SoftDeletes, SetCurrency;

    const TYPE_COUNT = 'count';
    const TYPE_SUM   = 'sum';

    const TYPES = [
        self::TYPE_COUNT    => self::TYPE_COUNT,
        self::TYPE_SUM      => self::TYPE_SUM,
    ];

    protected $guarded = ['id'];

    protected $casts = [
        'bonusable_type'    => 'string',
        'type'              => 'string',
        'bonusable_id'      => 'integer',
        'bonus_quantity'    => 'integer',
        'bonus_stock_id'    => 'integer',
        'value'             => 'integer',
        'status'            => 'bool',
        'expired_at'        => 'datetime',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    public function bonusable(): MorphTo
    {
        return $this->morphTo();
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class, 'bonus_stock_id');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function getRateValueAttribute(): float|int|null
    {
        if (request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*')) {
            return $this->type === self::TYPE_SUM ? $this->value * $this->currency() : $this->value;
        }

        return $this->value;
    }

    public function scopeActive($query): Builder
    {
        /** @var Bonus $query */
        return $query->where('status', true)->whereDate('expired_at', '>', now());
    }

    public function scopeFilter($query, $filter) {
        $query
            ->when(data_get($filter, 'type'), fn($q, $type) => $q->where('type', $type))
            ->when(data_get($filter, 'status'), fn($q, $status) => $q->where('status', $status))
            ->when(data_get($filter, 'shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
            ->when(data_get($filter, 'bonus_id'), fn($q, $bonusId) => $q->where('bonusable_id', $bonusId))
            ->when(data_get($filter, 'expired_at_from'),
                fn($q, $from) => $q->where('expired_at', '>=', date('Y-m-d 00:00:01', strtotime($from)))
            )
            ->when(data_get($filter, 'expired_at_to'),
                fn($q, $to) => $q->where('expired_at', '>=', date('Y-m-d 00:00:01', strtotime($to)))
            )
            ->when(data_get($filter, 'bonus_type'),
                fn($q, $bonusType) => $q->where('bonusable_type', $bonusType === 'stock' ? Stock::class : Shop::class)
            )
            ->when(data_get($filter, 'bonus_stock_id'),
                fn($q, $bonusStockId) => $q->where('bonus_stock_id', $bonusStockId)
            );
    }
}
