<?php

namespace App\Models;

use Database\Factories\ProductPropertiesFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ProductProperties
 *
 * @property int $id
 * @property int $product_id
 * @property string $locale
 * @property string $key
 * @property string|null $value
 * @property string|null $deleted_at
 * @method static ProductPropertiesFactory factory(...$parameters)
 * @method static Builder|ProductProperties newModelQuery()
 * @method static Builder|ProductProperties newQuery()
 * @method static Builder|ProductProperties query()
 * @method static Builder|ProductProperties whereId($value)
 * @method static Builder|ProductProperties whereKey($value)
 * @method static Builder|ProductProperties whereLocale($value)
 * @method static Builder|ProductProperties whereProductId($value)
 * @method static Builder|ProductProperties whereValue($value)
 * @mixin Eloquent
 */
class ProductProperties extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;
}
