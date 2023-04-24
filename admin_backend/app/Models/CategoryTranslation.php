<?php

namespace App\Models;

use Database\Factories\CategoryTranslationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\CategoryTranslation
 *
 * @property int $id
 * @property int $category_id
 * @property string $locale
 * @property string $title
 * @property string|null $description
 * @method static CategoryTranslationFactory factory(...$parameters)
 * @method static Builder|CategoryTranslation newModelQuery()
 * @method static Builder|CategoryTranslation newQuery()
 * @method static Builder|CategoryTranslation query()
 * @method static Builder|CategoryTranslation whereCategoryId($value)
 * @method static Builder|CategoryTranslation whereDescription($value)
 * @method static Builder|CategoryTranslation whereId($value)
 * @method static Builder|CategoryTranslation whereLocale($value)
 * @method static Builder|CategoryTranslation whereTitle($value)
 * @mixin Eloquent
 */
class CategoryTranslation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;
}
