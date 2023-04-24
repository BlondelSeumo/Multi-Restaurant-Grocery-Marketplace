<?php

namespace App\Models;

use Database\Factories\BannerTranslationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BannerTranslation
 *
 * @property int $id
 * @property int $banner_id
 * @property string $locale
 * @property string $title
 * @property string|null $description
 * @property string|null $button_text
 * @method static BannerTranslationFactory factory(...$parameters)
 * @method static Builder|BannerTranslation newModelQuery()
 * @method static Builder|BannerTranslation newQuery()
 * @method static Builder|BannerTranslation query()
 * @method static Builder|BannerTranslation whereBannerId($value)
 * @method static Builder|BannerTranslation whereButtonText($value)
 * @method static Builder|BannerTranslation whereDescription($value)
 * @method static Builder|BannerTranslation whereId($value)
 * @method static Builder|BannerTranslation whereLocale($value)
 * @method static Builder|BannerTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BannerTranslation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;
}
