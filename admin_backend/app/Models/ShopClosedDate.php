<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Tag
 *
 * @property int $id
 * @property int $shop_id
 * @property Carbon|null $date
 * @property Shop|null $shop
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @method static Builder|ShopClosedDate filter($query, $filter)
 * @method static Builder|ShopClosedDate newModelQuery()
 * @method static Builder|ShopClosedDate newQuery()
 * @method static Builder|ShopClosedDate query()
 * @method static Builder|ShopClosedDate whereCreatedAt($value)
 * @method static Builder|ShopClosedDate whereId($value)
 * @method static Builder|ShopClosedDate whereShopId($value)
 * @method static Builder|ShopClosedDate whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ShopClosedDate extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeFilter($query, array $filter) {
        $query
            ->when(data_get($filter, 'shop_id'),    fn($q, $shopId)     => $q->where('shop_id', $shopId))
            ->when(isset($filter['deleted_at']),        fn($q)              => $q->onlyTrashed())
            ->when(data_get($filter, 'date_from'),  fn($q, $dateFrom)   => $q->where('date', '>=', $dateFrom))
            ->when(data_get($filter, 'date_to'),    fn($q, $dateTo)     => $q->where('date', '<=', $dateTo));
    }
}
