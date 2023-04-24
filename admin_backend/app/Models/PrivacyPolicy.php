<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\PrivacyPolicy
 *
 * @property int $id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read PrivacyPolicyTranslation|null $translation
 * @property-read Collection|PrivacyPolicyTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static Builder|PrivacyPolicy newModelQuery()
 * @method static Builder|PrivacyPolicy newQuery()
 * @method static Builder|PrivacyPolicy query()
 * @method static Builder|PrivacyPolicy whereCreatedAt($value)
 * @method static Builder|PrivacyPolicy whereId($value)
 * @method static Builder|PrivacyPolicy whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PrivacyPolicy extends Model
{
    use HasFactory, SoftDeletes;

    // Translations
    public function translations(): HasMany
    {
        return $this->hasMany(PrivacyPolicyTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(PrivacyPolicyTranslation::class);
    }
}
