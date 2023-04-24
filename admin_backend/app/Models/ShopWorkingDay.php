<?php

namespace App\Models;

use Database\Factories\ShopWorkingDayFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Eloquent;

/**
 * App\Models\ShopWorkingDay
 *
 * @property int $id
 * @property int $shop_id
 * @property string $day
 * @property string|null $from
 * @property string|null $to
 * @property boolean|null $disabled
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static ShopWorkingDayFactory factory(...$parameters)
 * @method static Builder|ShopWorkingDay filter($array = [])
 * @method static Builder|ShopWorkingDay newModelQuery()
 * @method static Builder|ShopWorkingDay newQuery()
 * @method static Builder|ShopWorkingDay query()
 * @method static Builder|ShopWorkingDay whereCreatedAt($value)
 * @method static Builder|ShopWorkingDay whereUpdatedAt($value)
 * @method static Builder|ShopWorkingDay whereId($value)
 * @method static Builder|ShopWorkingDay whereShopId($value)
 * @method static Builder|ShopWorkingDay whereDay($value)
 * @method static Builder|ShopWorkingDay whereFrom($value)
 * @method static Builder|ShopWorkingDay whereTo($value)
 * @mixin Eloquent
 */
class ShopWorkingDay extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const MONDAY    = 'monday';
    const TUESDAY   = 'tuesday';
    const WEDNESDAY = 'wednesday';
    const THURSDAY  = 'thursday';
    const FRIDAY    = 'friday';
    const SATURDAY  = 'saturday';
    const SUNDAY    = 'sunday';

    const DAYS = [
        self::MONDAY    => self::MONDAY,
        self::TUESDAY   => self::TUESDAY,
        self::WEDNESDAY => self::WEDNESDAY,
        self::THURSDAY  => self::THURSDAY,
        self::FRIDAY    => self::FRIDAY,
        self::SATURDAY  => self::SATURDAY,
        self::SUNDAY    => self::SUNDAY,
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function scopeFilter($query, $filter = [])
    {
        return $query
            ->when(data_get($filter, 'day'),        fn($q, $day)        => $q->where('day', $day))
            ->when(isset($filter['deleted_at']),        fn($q)              => $q->onlyTrashed())
            ->when(data_get($filter, 'shop_id'),    fn($q, $shopId)     => $q->where('shop_id', $shopId))
            ->when(data_get($filter, 'from'),       fn($q, $from)       => $q->where('from', '>=', $from))
            ->when(data_get($filter, 'to'),         fn($q, $to)         => $q->where('to', '<=', $to))
            ->when(data_get($filter, 'disabled'),   fn($q, $disabled)   => $q->where('disabled', $disabled));
    }
}
