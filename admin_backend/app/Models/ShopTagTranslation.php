<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ShopTagTranslation
 *
 * @property int $id
 * @property int $shop_tag_id
 * @property string $locale
 * @property string $title
 * @method static Builder|ShopTagTranslation newModelQuery()
 * @method static Builder|ShopTagTranslation newQuery()
 * @method static Builder|ShopTagTranslation query()
 * @method static Builder|ShopTagTranslation whereId($value)
 * @method static Builder|ShopTagTranslation whereLocale($value)
 * @method static Builder|ShopTagTranslation whereTitle($value)
 * @mixin Eloquent
 */
class ShopTagTranslation extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;

}
