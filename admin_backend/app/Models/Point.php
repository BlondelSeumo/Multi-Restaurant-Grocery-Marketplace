<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Point
 *
 * @property int $id
 * @property int|null $shop_id
 * @property Shop|null $shop
 * @property string $type
 * @property float $price
 * @property int $value
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|Point newModelQuery()
 * @method static Builder|Point newQuery()
 * @method static Builder|Point query()
 * @method static Builder|Point whereActive($value)
 * @method static Builder|Point whereCreatedAt($value)
 * @method static Builder|Point whereId($value)
 * @method static Builder|Point wherePrice($value)
 * @method static Builder|Point whereShopId($value)
 * @method static Builder|Point whereType($value)
 * @method static Builder|Point whereUpdatedAt($value)
 * @method static Builder|Point whereValue($value)
 * @mixin Eloquent
 */
class Point extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = [];

    protected $casts = [
        'type'          => 'string',
        'price'         => 'string',
        'value'         => 'string',
        'active'        => 'bool',
        'created_at'    => 'datetime:Y-m-d H:i:s',
        'updated_at'    => 'datetime:Y-m-d H:i:s',
    ];

    public static function getActualPoint(string $amount)
    {
        $point = self::where('active', 1)
            ->where('value', '<=', (int) $amount)
            ->orderByDesc('value')
            ->first();

        return $point?->type == 'percent' ? ($amount / 100) * ($point?->price ?? 0) : ($point?->price ?? 0);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }
}
