<?php

namespace App\Models;

use App\Traits\Likable;
use App\Traits\Loadable;
use Database\Factories\BannerFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Banner
 *
 * @property int $id
 * @property string|null $url
 * @property string|null $img
 * @property int $active
 * @property string $type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property bool $clickable
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @property-read Collection|Shop[] $shops
 * @property-read int|null $shops_count
 * @property-read BannerTranslation|null $translation
 * @property-read Collection|BannerTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static BannerFactory factory(...$parameters)
 * @method static Builder|Banner newModelQuery()
 * @method static Builder|Banner newQuery()
 * @method static Builder|Banner query()
 * @method static Builder|Banner whereActive($value)
 * @method static Builder|Banner whereClickable($value)
 * @method static Builder|Banner whereCreatedAt($value)
 * @method static Builder|Banner whereDeletedAt($value)
 * @method static Builder|Banner whereId($value)
 * @method static Builder|Banner whereImg($value)
 * @method static Builder|Banner whereProducts($value)
 * @method static Builder|Banner whereShopId($value)
 * @method static Builder|Banner whereUpdatedAt($value)
 * @method static Builder|Banner whereUrl($value)
 * @mixin Eloquent
 */
class Banner extends Model
{
    use HasFactory, Loadable, Likable, SoftDeletes;

    protected $guarded = ['id'];

    const TYPES = [
        'banner',
    ];

    public function shops(): BelongsToMany
    {
        return $this->belongsToMany(Shop::class, BannerShop::class);
    }

    // Translations
    public function translations(): HasMany
    {
        return $this->hasMany(BannerTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(BannerTranslation::class);
    }
}
