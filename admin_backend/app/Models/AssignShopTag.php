<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\ShopTag
 *
 * @property int $shop_tag_id
 * @property int $shop_id
 * @property Shop|null $shop
 * @property ShopTag|null $shop_tag
 * @method static Builder|AssignShopTag newModelQuery()
 * @method static Builder|AssignShopTag newQuery()
 * @method static Builder|AssignShopTag query()
 * @method static Builder|AssignShopTag whereId($value)
 * @method static Builder|AssignShopTag whereShopId($value)
 * @method static Builder|AssignShopTag whereShopTagId($value)
 * @mixin Eloquent
 */
class AssignShopTag extends Model
{
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function shopTag(): BelongsTo
    {
        return $this->belongsTo(ShopTag::class);
    }
}
