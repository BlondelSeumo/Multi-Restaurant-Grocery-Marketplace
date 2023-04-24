<?php

namespace App\Models;

use Database\Factories\ProductTranslationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ProductTranslation
 *
 * @property int $id
 * @property int $product_id
 * @property string $locale
 * @property string $title
 * @property string|null $description
 * @method static ProductTranslationFactory factory(...$parameters)
 * @method static Builder|ProductTranslation newModelQuery()
 * @method static Builder|ProductTranslation newQuery()
 * @method static Builder|ProductTranslation query()
 * @method static Builder|ProductTranslation whereDescription($value)
 * @method static Builder|ProductTranslation whereId($value)
 * @method static Builder|ProductTranslation whereLocale($value)
 * @method static Builder|ProductTranslation whereProductId($value)
 * @method static Builder|ProductTranslation whereTitle($value)
 * @mixin Eloquent
 */
class ProductTranslation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;
}
