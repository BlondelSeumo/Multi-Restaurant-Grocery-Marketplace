<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;

/**
 * App\Models\Cart
 *
 * @property int $id
 * @property int $shop_id
 * @property int $owner_id
 * @property double $total_price
 * @property double $rate_total_price
 * @property double $receipt_discount
 * @property double $receipt_count
 * @property int $status
 * @property int $currency_id
 * @property int $rate
 * @property boolean $group
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read UserCart $userCart
 * @property-read User|BelongsTo $user
 * @property-read Shop|BelongsTo $shop
 * @property-read Shop|BelongsTo $currency
 * @property-read UserCart[]|HasMany $userCarts
 * @property-read int|null $user_carts_count
 * @method static Builder|Cart newModelQuery()
 * @method static Builder|Cart newQuery()
 * @method static Builder|Cart query()
 * @method static Builder|Cart whereCreatedAt($value)
 * @method static Builder|Cart whereId($value)
 * @method static Builder|Cart whereOwnerId($value)
 * @method static Builder|Cart whereStatus($value)
 * @method static Builder|Cart whereStockId($value)
 * @method static Builder|Cart whereTotalPrice($value)
 * @method static Builder|Cart whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Cart extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'status'        => 'bool',
        'group'         => 'bool',
        'rate'          => 'float',
        'total_price'   => 'float',
        'created_at'    => 'datetime:Y-m-d H:i:s',
        'updated_at'    => 'datetime:Y-m-d H:i:s',
    ];

    public function getRateTotalPriceAttribute(): float|int|null
    {
        if (request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*')) {
            return $this->total_price * $this->rate;
        }

        return $this->total_price;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class,'owner_id','id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function userCarts(): HasMany
    {
        return $this->hasMany(UserCart::class);
    }

    public function userCart(): HasOne
    {
        return $this->hasOne(UserCart::class);
    }
}
