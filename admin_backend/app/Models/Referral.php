<?php

namespace App\Models;

use App\Traits\Loadable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Referral
 *
 * @property int $id
 * @property double $price_from
 * @property double $price_to
 * @property Carbon|null $expired_at
 * @property string $img
 * @property Translation|null $translation
 * @property Collection|Translation[] $translations
 * @property int $translations_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|Referral newModelQuery()
 * @method static Builder|Referral newQuery()
 * @method static Builder|Referral query()
 * @method static Builder|Referral whereCreatedAt($value)
 * @method static Builder|Referral whereId($value)
 * @method static Builder|Referral whereUpdatedAt($value)
 * @method static Builder|Referral whereDeletedAt($value)
 * @mixin Eloquent
 */
class Referral extends Model
{
    use SoftDeletes, Loadable;

    protected $guarded = ['id'];

    // Translations
    public function translations(): HasMany
    {
        return $this->hasMany(ReferralTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(ReferralTranslation::class);
    }
}
