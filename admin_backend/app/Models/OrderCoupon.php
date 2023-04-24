<?php

namespace App\Models;

use Database\Factories\OrderCouponFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\OrderCoupon
 *
 * @property int $id
 * @property int $order_id
 * @property int $user_id
 * @property string $name
 * @property float|null $price
 * @method static OrderCouponFactory factory(...$parameters)
 * @method static Builder|OrderCoupon newModelQuery()
 * @method static Builder|OrderCoupon newQuery()
 * @method static Builder|OrderCoupon query()
 * @method static Builder|OrderCoupon whereId($value)
 * @method static Builder|OrderCoupon whereName($value)
 * @method static Builder|OrderCoupon whereOrderId($value)
 * @method static Builder|OrderCoupon wherePrice($value)
 * @method static Builder|OrderCoupon whereUserId($value)
 * @mixin Eloquent
 */
class OrderCoupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;
}
