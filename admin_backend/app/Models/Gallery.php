<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * App\Models\Gallery
 *
 * @property int $id
 * @property string $title
 * @property string $loadable_type
 * @property int $loadable_id
 * @property string|null $type
 * @property string|null $path
 * @property string|null $mime
 * @property string|null $size
 * @property-read Model|Eloquent $loadable
 * @method static Builder|Gallery newModelQuery()
 * @method static Builder|Gallery newQuery()
 * @method static Builder|Gallery query()
 * @method static Builder|Gallery whereId($value)
 * @method static Builder|Gallery whereLoadableId($value)
 * @method static Builder|Gallery whereLoadableType($value)
 * @method static Builder|Gallery whereMime($value)
 * @method static Builder|Gallery wherePath($value)
 * @method static Builder|Gallery whereSize($value)
 * @method static Builder|Gallery whereTitle($value)
 * @method static Builder|Gallery whereType($value)
 * @mixin Eloquent
 */
class Gallery extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    const TYPES = [
        'restaurant/logo', 'restaurant/background', 'shops/logo', 'shops/background', 'deliveryman/settings',
        'deliveryman', 'shops', 'restaurant', 'banners', 'brands', 'blogs', 'categories', 'coupons', 'discounts',
        'extras', 'reviews', 'order_refunds', 'users', 'products', 'languages', 'referral', 'shop-tags', 'receipts',
        'shop-galleries'
    ];

    public function loadable(): MorphTo
    {
        return $this->morphTo('loadable');
    }
}
