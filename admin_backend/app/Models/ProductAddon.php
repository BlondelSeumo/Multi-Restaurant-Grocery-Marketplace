<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\Product
 *
 * @property int $id
 * @property int $product_id
 * @property int $addon_id
 * @property Product|null $addon
 * @property Product|null $product
 * @method static ProductFactory factory(...$parameters)
 * @method static Builder|Product newModelQuery()
 * @method static Builder|Product newQuery()
 * @method static Builder|Product query()
 * @method static Builder|Product whereAddonId($value)
 * @method static Builder|Product whereProductId($value)
 * @mixin Eloquent
 */
class ProductAddon extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'addon_id');
    }
}
