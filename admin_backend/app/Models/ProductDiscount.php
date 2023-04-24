<?php

namespace App\Models;

use Database\Factories\ProductDiscountFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ProductDiscount
 *
 * @property int $id
 * @property int $discount_id
 * @property int $product_id
 * @method static ProductDiscountFactory factory(...$parameters)
 * @method static Builder|ProductDiscount newModelQuery()
 * @method static Builder|ProductDiscount newQuery()
 * @method static Builder|ProductDiscount query()
 * @method static Builder|ProductDiscount whereDiscountId($value)
 * @method static Builder|ProductDiscount whereId($value)
 * @method static Builder|ProductDiscount whereShopProductId($value)
 * @mixin Eloquent
 */
class ProductDiscount extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded  = ['id'];

}
