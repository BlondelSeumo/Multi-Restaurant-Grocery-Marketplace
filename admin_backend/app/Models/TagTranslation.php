<?php

namespace App\Models;

use Database\Factories\TagTranslationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\TagTranslation
 *
 * @property int $id
 * @property int $tag_id
 * @property string $locale
 * @property string $title
 * @property string|null $description
 * @method static Builder|TagTranslation newModelQuery()
 * @method static Builder|TagTranslation newQuery()
 * @method static Builder|TagTranslation query()
 * @method static Builder|TagTranslation whereAddress($value)
 * @method static Builder|TagTranslation whereDescription($value)
 * @method static Builder|TagTranslation whereId($value)
 * @method static Builder|TagTranslation whereLocale($value)
 * @method static Builder|TagTranslation whereShopId($value)
 * @method static Builder|TagTranslation whereTitle($value)
 * @mixin Eloquent
 */
class TagTranslation extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];
}
