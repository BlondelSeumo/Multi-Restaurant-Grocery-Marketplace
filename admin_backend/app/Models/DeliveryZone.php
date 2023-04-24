<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\DeliveryZone
 *
 * @property int $id
 * @property int $shop_id
 * @property array $address
 * @property Shop|null $shop
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Carbon $deleted_at
 * @method static Builder|DeliveryZone newModelQuery()
 * @method static Builder|DeliveryZone newQuery()
 * @method static Builder|DeliveryZone query()
 * @method static Builder|DeliveryZone whereCreatedAt($value)
 * @method static Builder|DeliveryZone whereId($value)
 * @method static Builder|DeliveryZone whereStockId($value)
 * @method static Builder|DeliveryZone whereUpdatedAt($value)
 * @mixin Eloquent
 */
class DeliveryZone extends Model
{
    use HasFactory, SoftDeletes;

    const TTL = 8640000000; // 100000 day

    protected $guarded = ['id'];

    protected $table = 'shop_delivery_zone';

    protected $casts = [
        'address'       => 'array',
        'created_at'    => 'datetime:Y-m-d H:i:s',
        'updated_at'    => 'datetime:Y-m-d H:i:s',
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    /**
     * @return mixed
     */
    public static function list(): mixed
    {
        return Cache::remember('delivery-zone-list', self::TTL, function () {
            return self::orderByDesc('id')->get();
        });
    }
}
