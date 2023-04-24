<?php

namespace App\Models\Booking;

use App\Models\Shop;
use App\Models\ShopTranslation;
use App\Traits\Loadable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\ShopSection
 *
 * @property int $id
 * @property int $shop_id
 * @property string $area
 * @property string $img
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property Shop|null $shop
 * @property ShopTranslation|null $translation
 * @property int|null $translations_count
 * @property Collection|ShopTranslation[] $translations
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self filter(array $filter)
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereDeletedAt($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ShopSection extends Model
{
    use Loadable, SoftDeletes;

    protected $guarded = ['id'];

    // Shop
    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    // Translations
    public function translations(): HasMany
    {
        return $this->hasMany(ShopSectionTranslation::class, 'shop_section_id');
    }

    public function translation(): HasOne
    {
        return $this->hasOne(ShopSectionTranslation::class, 'shop_section_id');
    }

    public function scopeFilter($query, $filter) {
        $query->when(data_get($filter, 'shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId));
    }

}
