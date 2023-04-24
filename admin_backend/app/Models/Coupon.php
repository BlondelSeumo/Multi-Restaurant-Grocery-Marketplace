<?php

namespace App\Models;

use App\Traits\Loadable;
use Database\Factories\CouponFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Coupon
 *
 * @property int $id
 * @property int $shop_id
 * @property string $name
 * @property string $type
 * @property int $qty
 * @property float $price
 * @property string $expired_at
 * @property string|null $img
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @property-read Collection|OrderCoupon[] $orderCoupon
 * @property-read int|null $order_coupon_count
 * @property-read Shop $shop
 * @property-read CouponTranslation|null $translation
 * @property-read Collection|CouponTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static Builder|Coupon checkCoupon($coupon)
 * @method static CouponFactory factory(...$parameters)
 * @method static Builder|Coupon filter($array)
 * @method static Builder|Coupon newModelQuery()
 * @method static Builder|Coupon newQuery()
 * @method static Builder|Coupon query()
 * @method static Builder|Coupon increment($column, $amount = 1, array $extra = [])
 * @method static Builder|Coupon decrement($column, $amount = 1, array $extra = [])
 * @method static Builder|Coupon whereCreatedAt($value)
 * @method static Builder|Coupon whereExpiredAt($value)
 * @method static Builder|Coupon whereId($value)
 * @method static Builder|Coupon whereImg($value)
 * @method static Builder|Coupon whereName($value)
 * @method static Builder|Coupon wherePrice($value)
 * @method static Builder|Coupon whereQty($value)
 * @method static Builder|Coupon whereShopId($value)
 * @method static Builder|Coupon whereType($value)
 * @method static Builder|Coupon whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Coupon extends Model
{
    use HasFactory, Loadable, SoftDeletes;

    protected $guarded = ['id'];

    public function translations(): HasMany
    {
        return $this->hasMany(CouponTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(CouponTranslation::class);
    }

    public function orderCoupon(): HasMany
    {
        return $this->hasMany(OrderCoupon::class, 'name', 'name');
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public static function scopeCheckCoupon($query, $coupon) {
        return $query->where('name', $coupon)
            ->where('qty', '>', 0)
            ->whereDate('expired_at', '>', now());
    }

    public function scopeFilter($query, $array)
    {
        $query
            ->when(data_get($array, 'shop_id'), function ($q, $shopId) {
                $q->where('shop_id', $shopId);
            })
            ->when(data_get($array, 'type'), function ($q, $type) {
                $q->where('type', $type);
            });
    }
}
