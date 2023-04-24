<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\CartDetail
 *
 * @property int $id
 * @property int $stock_id
 * @property int $user_cart_id
 * @property int $parent_id
 * @property int $quantity
 * @property float|null $price
 * @property float|null $discount
 * @property int $bonus
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property double $rate_price
 * @property double $rate_discount
 * @property-read Stock|null $stock
 * @property-read CartDetail|null $parent
 * @property-read Collection|CartDetail[] $children
 * @property-read UserCart $userCart
 * @method static Builder|CartDetail newModelQuery()
 * @method static Builder|CartDetail newQuery()
 * @method static Builder|CartDetail query()
 * @method static Builder|CartDetail whereBonus($value)
 * @method static Builder|CartDetail whereCreatedAt($value)
 * @method static Builder|CartDetail whereId($value)
 * @method static Builder|CartDetail wherePrice($value)
 * @method static Builder|CartDetail whereQuantity($value)
 * @method static Builder|CartDetail whereStockId($value)
 * @method static Builder|CartDetail whereUpdatedAt($value)
 * @method static Builder|CartDetail whereUserCartId($value)
 * @mixin Eloquent
 */
class CartDetail extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'bonus' => 'bool'
    ];

    public function getRatePriceAttribute(): float
    {
        return request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*') ?
            $this->price * $this->userCart?->cart?->rate :
            $this->price;
    }

    public function getRateDiscountAttribute(): float
    {
        return request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*') ?
            $this->discount * $this->userCart?->cart?->rate :
            $this->discount;
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function userCart(): BelongsTo
    {
        return $this->belongsTo(UserCart::class);
    }
}
