<?php

namespace App\Models;

use Database\Factories\BlogTranslationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\BlogTranslation
 *
 * @property int $id
 * @property int $blog_id
 * @property string $locale
 * @property string $title
 * @property string|null $short_desc
 * @property string|null $description
 * @method static BlogTranslationFactory factory(...$parameters)
 * @method static Builder|BlogTranslation newModelQuery()
 * @method static Builder|BlogTranslation newQuery()
 * @method static Builder|BlogTranslation query()
 * @method static Builder|BlogTranslation whereBlogId($value)
 * @method static Builder|BlogTranslation whereDescription($value)
 * @method static Builder|BlogTranslation whereId($value)
 * @method static Builder|BlogTranslation whereLocale($value)
 * @method static Builder|BlogTranslation whereShortDesc($value)
 * @method static Builder|BlogTranslation whereTitle($value)
 * @mixin Eloquent
 */
class BlogTranslation extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];
}
