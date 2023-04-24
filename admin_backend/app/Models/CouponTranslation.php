<?php

namespace App\Models;

use Database\Factories\CouponTranslationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CouponTranslation
 *
 * @property int $id
 * @property int $coupon_id
 * @property string $locale
 * @property string $title
 * @property string|null $description
 * @method static CouponTranslationFactory factory(...$parameters)
 * @method static Builder|CouponTranslation newModelQuery()
 * @method static Builder|CouponTranslation newQuery()
 * @method static Builder|CouponTranslation query()
 * @method static Builder|CouponTranslation whereCouponId($value)
 * @method static Builder|CouponTranslation whereDescription($value)
 * @method static Builder|CouponTranslation whereId($value)
 * @method static Builder|CouponTranslation whereLocale($value)
 * @method static Builder|CouponTranslation whereTitle($value)
 * @mixin Eloquent
 */
class CouponTranslation extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    public $guarded = ['id'];
}
