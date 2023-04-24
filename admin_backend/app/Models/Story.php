<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;

/**
 * App\Models\Story
 *
 * @property int $id
 * @property array $file_urls
 * @property int $product_id
 * @property int $shop_id
 * @property boolean $active
 * @property Product|null $product
 * @property Shop|null $shop
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method static Builder|Story newModelQuery()
 * @method static Builder|Story newQuery()
 * @method static Builder|Story query()
 * @method static Builder|Story whereCreatedAt($value)
 * @method static Builder|Story whereId($value)
 * @method static Builder|Story whereStockId($value)
 * @method static Builder|Story whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Story extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'active'        => 'boolean',
        'file_urls'     => 'array',
        'created_at'    => 'datetime:Y-m-d H:i:s',
        'updated_at'    => 'datetime:Y-m-d H:i:s',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
