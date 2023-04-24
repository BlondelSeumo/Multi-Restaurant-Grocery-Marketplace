<?php

namespace App\Models;

use Database\Factories\UnitTranslationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UnitTranslation
 *
 * @property int $id
 * @property int $unit_id
 * @property string $locale
 * @property string $title
 * @method static UnitTranslationFactory factory(...$parameters)
 * @method static Builder|UnitTranslation newModelQuery()
 * @method static Builder|UnitTranslation newQuery()
 * @method static Builder|UnitTranslation query()
 * @method static Builder|UnitTranslation whereId($value)
 * @method static Builder|UnitTranslation whereLocale($value)
 * @method static Builder|UnitTranslation whereTitle($value)
 * @method static Builder|UnitTranslation whereUnitId($value)
 * @mixin Eloquent
 */
class UnitTranslation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;
}
