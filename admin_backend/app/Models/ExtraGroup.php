<?php

namespace App\Models;

use Carbon\Carbon;
use Database\Factories\ExtraGroupFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ExtraGroup
 *
 * @property int $id
 * @property string|null $type
 * @property int $active
 * @property Carbon|null $deleted_at
 * @property-read Collection|ExtraValue[] $extraValues
 * @property-read int|null $extra_values_count
 * @property-read ExtraGroupTranslation|null $translation
 * @property-read Collection|ExtraGroupTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static ExtraGroupFactory factory(...$parameters)
 * @method static Builder|ExtraGroup newModelQuery()
 * @method static Builder|ExtraGroup newQuery()
 * @method static Builder|ExtraGroup query()
 * @method static Builder|ExtraGroup whereActive($value)
 * @method static Builder|ExtraGroup whereId($value)
 * @method static Builder|ExtraGroup whereType($value)
 * @mixin Eloquent
 */
class ExtraGroup extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;

    const TYPES = [
        'color',
        'text',
        'image'
    ];

    public function getTypes(): array
    {
        return self::TYPES;
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ExtraGroupTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(ExtraGroupTranslation::class);
    }

    public function extraValues(): HasMany
    {
        return $this->hasMany(ExtraValue::class);
    }
}
