<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ShopCategory
 *
 * @property int $id
 * @property int $shop_id
 * @property int $category_id
 * @property Carbon|null $deleted_at
 * @property-read Collection|Category[] $categories
 * @property-read Collection|Shop[] $shops
 * @property-read int|null $categories_count
 * @property-read int|null $shops_count
 * @method static Builder|ShopCategory newModelQuery()
 * @method static Builder|ShopCategory newQuery()
 * @method static Builder|ShopCategory query()
 * @method static Builder|ShopCategory whereCategoryId($value)
 * @method static Builder|ShopCategory whereId($value)
 * @method static Builder|ShopCategory whereShopId($value)
 * @mixin Eloquent
 */
class ShopCategory extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    public $guarded = ['id'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
