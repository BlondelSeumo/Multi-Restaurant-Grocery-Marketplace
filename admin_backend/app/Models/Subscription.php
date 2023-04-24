<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Subscription
 *
 * @property int $id
 * @property string $type
 * @property float $price
 * @property int $month
 * @property int $active
 * @property string $title
 * @property int $product_limit
 * @property int $order_limit
 * @property boolean $with_report
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|Subscription newModelQuery()
 * @method static Builder|Subscription newQuery()
 * @method static Builder|Subscription onlyTrashed()
 * @method static Builder|Subscription query()
 * @method static Builder|Subscription whereActive($value)
 * @method static Builder|Subscription whereCreatedAt($value)
 * @method static Builder|Subscription whereDeletedAt($value)
 * @method static Builder|Subscription whereId($value)
 * @method static Builder|Subscription whereMonth($value)
 * @method static Builder|Subscription wherePrice($value)
 * @method static Builder|Subscription whereType($value)
 * @method static Builder|Subscription whereUpdatedAt($value)
 * @method static Builder|Subscription withTrashed()
 * @method static Builder|Subscription withoutTrashed()
 * @mixin Eloquent
 */
class Subscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const TTL = 2592000; // 30 days

    public static function subscriptionsList()
    {
        return Cache::remember('subscriptions-list', self::TTL, function () {
            return self::all();
        });
    }
}
