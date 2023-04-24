<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BannerShop
 *
 * @property int $id
 * @property int $shop_id
 * @property int $banner_id
 * @property Banner $banner
 * @property Shop $shop
 * @method static Builder|Banner newModelQuery()
 * @method static Builder|Banner newQuery()
 * @method static Builder|Banner query()
 * @method static Builder|Banner whereBannerId($value)
 * @method static Builder|Banner whereShopId($value)
 * @mixin Eloquent
 */
class BannerShop extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function banner(): BelongsTo
    {
        return $this->belongsTo(Banner::class);
    }
}
