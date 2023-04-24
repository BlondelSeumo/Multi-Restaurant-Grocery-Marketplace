<?php

namespace App\Models;

use App\Traits\Loadable;
use App\Traits\SetCurrency;
use Database\Factories\DiscountFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Discount
 *
 * @property int $id
 * @property int $shop_id
 * @property string $type
 * @property float $price
 * @property string $start
 * @property string|null $end
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $img
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @method static DiscountFactory factory(...$parameters)
 * @method static Builder|Discount filter($array)
 * @method static Builder|Discount newModelQuery()
 * @method static Builder|Discount newQuery()
 * @method static Builder|Discount query()
 * @method static Builder|Discount updatedDate($updatedDate)
 * @method static Builder|Discount whereActive($value)
 * @method static Builder|Discount whereCreatedAt($value)
 * @method static Builder|Discount whereEnd($value)
 * @method static Builder|Discount whereId($value)
 * @method static Builder|Discount whereImg($value)
 * @method static Builder|Discount wherePrice($value)
 * @method static Builder|Discount whereShopId($value)
 * @method static Builder|Discount whereStart($value)
 * @method static Builder|Discount whereType($value)
 * @method static Builder|Discount whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Discount extends Model
{
    use HasFactory, SetCurrency, Loadable, SoftDeletes;

    protected $guarded = ['id'];

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, ProductDiscount::class);
    }

    public function scopeUpdatedDate($query, $updatedDate)
    {
        return $query->where('updated_at', '>', $updatedDate);
    }

    /* Filter Scope */
    public function scopeFilter($value, array $filter)
    {
        return $value
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->when(data_get($filter, 'type'), function ($q, $type) {
                $q->where('type', $type);
            })
            ->when(data_get($filter, 'active'), function ($q, $active) {
                $q->where('active', $active);
            })->when(data_get($filter, 'shop_id'), function ($q, $shopId) {
                $q->where('shop_id', $shopId);
            });
    }}
