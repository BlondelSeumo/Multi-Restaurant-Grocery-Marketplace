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
 * App\Models\ShopPayment
 *
 * @property int $id
 * @property int $payment_id
 * @property int $shop_id
 * @property int $status
 * @property string|null $client_id
 * @property string|null $secret_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $merchant_email
 * @property string|null $payment_key
 * @property-read Payment|null $payment
 * @property-read Shop|null $shop
 * @method static Builder|ShopPayment newModelQuery()
 * @method static Builder|ShopPayment newQuery()
 * @method static Builder|ShopPayment query()
 * @method static Builder|ShopPayment filter(array $filter)
 * @method static Builder|ShopPayment whereClientId($value)
 * @method static Builder|ShopPayment whereCreatedAt($value)
 * @method static Builder|ShopPayment whereId($value)
 * @method static Builder|ShopPayment whereMerchantEmail($value)
 * @method static Builder|ShopPayment wherePaymentId($value)
 * @method static Builder|ShopPayment wherePaymentKey($value)
 * @method static Builder|ShopPayment whereSecretId($value)
 * @method static Builder|ShopPayment whereShopId($value)
 * @method static Builder|ShopPayment whereStatus($value)
 * @method static Builder|ShopPayment whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ShopPayment extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function payment(): BelongsTo
    {
        return $this->belongsTo(Payment::class);
    }

    public function scopeFilter($query, array $filter) {
        $query
            ->when(data_get($filter, 'shop_id'),    fn($q, $shopId)     => $q->where('shop_id', $shopId))
            ->when(data_get($filter, 'payment_id'), fn($q, $paymentId)  => $q->where('payment_id', $paymentId))
            ->when(data_get($filter, 'status'),     fn($q, $status)     => $q->where('status', $status))
            ->when(data_get($filter, 'client_id'),  fn($q, $clientId)   => $q->where('client_id', $clientId))
            ->when(isset($filter['deleted_at']),        fn($q)              => $q->onlyTrashed())
            ->when(data_get($filter, 'secret_id'),  fn($q, $secretId)   => $q->where('secret_id', $secretId));
    }
}
