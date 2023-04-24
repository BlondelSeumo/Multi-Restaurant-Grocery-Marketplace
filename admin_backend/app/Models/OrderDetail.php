<?php

namespace App\Models;

use App\Traits\Notification;
use Database\Factories\OrderDetailFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\OrderDetail
 *
 * @property int $id
 * @property int $order_id
 * @property int $stock_id
 * @property int $parent_id
 * @property float $origin_price
 * @property float $total_price
 * @property float $tax
 * @property float $discount
 * @property float $rate_discount
 * @property int $quantity
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Order $order
 * @property-read Stock $stock
 * @property-read int $rate_total_price
 * @property-read int $rate_origin_price
 * @property-read int $rate_tax
 * @property-read boolean $bonus
 * @property-read OrderDetail $parent
 * @property-read Collection $children
 * @method static OrderDetailFactory factory(...$parameters)
 * @method static Builder|OrderDetail filter($array)
 * @method static Builder|OrderDetail newModelQuery()
 * @method static Builder|OrderDetail newQuery()
 * @method static Builder|OrderDetail query()
 * @method static Builder|OrderDetail updatedDate($updatedDate)
 * @method static Builder|OrderDetail whereCreatedAt($value)
 * @method static Builder|OrderDetail whereDiscount($value)
 * @method static Builder|OrderDetail whereId($value)
 * @method static Builder|OrderDetail whereOrderId($value)
 * @method static Builder|OrderDetail whereOriginPrice($value)
 * @method static Builder|OrderDetail whereQuantity($value)
 * @method static Builder|OrderDetail whereStockId($value)
 * @method static Builder|OrderDetail whereTax($value)
 * @method static Builder|OrderDetail whereTotalPrice($value)
 * @method static Builder|OrderDetail whereUpdatedAt($value)
 * @mixin Eloquent
 */
class OrderDetail extends Model
{
    use HasFactory, Notification, SoftDeletes;

    protected $guarded = ['id'];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class)->withTrashed();
    }

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class)->withTrashed();
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id')->withTrashed();
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id')->withTrashed();
    }

    public function getRateTotalPriceAttribute(): ?float
    {
        if (request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*')) {
            return $this->total_price * $this->order->rate;
        }

        return $this->total_price;
    }

    public function getRateDiscountAttribute(): ?float
    {
        if (request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*')) {
            return $this->discount * $this->order->rate;
        }

        return $this->discount;
    }

    public function getRateOriginPriceAttribute(): ?float
    {
        if (request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*')) {
            return $this->origin_price * $this->order->rate;
        }

        return $this->origin_price;
    }

    public function getRateTaxAttribute(): ?float
    {
        if (request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*')) {
            return $this->tax * $this->order->rate;
        }

        return $this->tax;
    }

    public function scopeUpdatedDate($query, $updatedDate)
    {
        return $query->where('updated_at', '>', $updatedDate);
    }
}
