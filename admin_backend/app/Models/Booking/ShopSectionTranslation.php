<?php

namespace App\Models\Booking;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ShopSectionTranslation
 *
 * @property int $id
 * @property int $title
 * @property int $locale
 * @property int $shop_section_id
 * @property ShopSection|null $shopSection
 * @property string|null $deleted_at
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self whereId($value)
 * @mixin Eloquent
 */
class ShopSectionTranslation extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;
}
