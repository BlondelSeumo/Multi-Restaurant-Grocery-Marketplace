<?php

namespace App\Models;

use Database\Factories\FaqFactory;
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
 * App\Models\Faq
 *
 * @property int $id
 * @property string $uuid
 * @property string|null $type
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read FaqTranslation|null $translation
 * @property-read Collection|FaqTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static FaqFactory factory(...$parameters)
 * @method static Builder|Faq newModelQuery()
 * @method static Builder|Faq newQuery()
 * @method static Builder|Faq query()
 * @method static Builder|Faq whereActive($value)
 * @method static Builder|Faq whereCreatedAt($value)
 * @method static Builder|Faq whereId($value)
 * @method static Builder|Faq whereType($value)
 * @method static Builder|Faq whereUpdatedAt($value)
 * @method static Builder|Faq whereUuid($value)
 * @mixin Eloquent
 */
class Faq extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function translations(): HasMany
    {
        return $this->hasMany(FaqTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(FaqTranslation::class);
    }
}
