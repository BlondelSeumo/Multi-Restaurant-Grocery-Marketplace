<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\StockAddon
 *
 * @property int $id
 * @property int $stock_id
 * @property int $addon_id
 * @property Product|null $addon
 * @property Stock|null $stock
 * @method static Builder|StockAddon newModelQuery()
 * @method static Builder|StockAddon newQuery()
 * @method static Builder|StockAddon query()
 * @method static Builder|StockAddon whereAddonId($value)
 * @method static Builder|StockAddon whereProductId($value)
 * @mixin Eloquent
 */
class StockAddon extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function stock(): BelongsTo
    {
        return $this->belongsTo(Stock::class);
    }

    public function addon(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'addon_id');
    }
}
