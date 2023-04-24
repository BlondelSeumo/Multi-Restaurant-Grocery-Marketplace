<?php

namespace App\Models;

use App\Traits\Payable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ShopSubscription
 *
 * @property int $id
 * @property int $shop_id
 * @property int $subscription_id
 * @property string|null $expired_at
 * @property float|null $price
 * @property string|null $type
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Shop $shop
 * @property-read Subscription|null $subscription
 * @property-read Transaction|null $transaction
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @method static Builder|ShopSubscription actualSubscription()
 * @method static Builder|ShopSubscription newModelQuery()
 * @method static Builder|ShopSubscription newQuery()
 * @method static Builder|ShopSubscription query()
 * @method static Builder|ShopSubscription whereActive($value)
 * @method static Builder|ShopSubscription whereCreatedAt($value)
 * @method static Builder|ShopSubscription whereExpiredAt($value)
 * @method static Builder|ShopSubscription whereId($value)
 * @method static Builder|ShopSubscription wherePrice($value)
 * @method static Builder|ShopSubscription whereShopId($value)
 * @method static Builder|ShopSubscription whereSubscriptionId($value)
 * @method static Builder|ShopSubscription whereType($value)
 * @method static Builder|ShopSubscription whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ShopSubscription extends Model
{
    use HasFactory, Payable, SoftDeletes;

    protected $guarded = ['id'];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function transaction(): MorphOne
    {
        return $this->morphOne( Transaction::class,'payable');
    }

    public function scopeActualSubscription($query)
    {
        return $query->where('active', 1)
            ->where('expired_at', '>=', now());
    }
}
