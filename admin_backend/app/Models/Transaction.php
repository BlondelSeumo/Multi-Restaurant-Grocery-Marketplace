<?php

namespace App\Models;

use Database\Factories\TransactionFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Transaction
 *
 * @property int $id
 * @property string $payable_type
 * @property int $payable_id
 * @property float $price
 * @property int|null $user_id
 * @property int|null $payment_sys_id
 * @property string|null $payment_trx_id
 * @property string|null $note
 * @property string|null $perform_time
 * @property string|null $refund_time
 * @property string $status
 * @property string $status_description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Order|Wallet|null $payable
 * @property-read Payment|null $paymentSystem
 * @property-read User|null $user
 * @method static TransactionFactory factory(...$parameters)
 * @method static Builder|Transaction filter($array = [])
 * @method static Builder|Transaction newModelQuery()
 * @method static Builder|Transaction newQuery()
 * @method static Builder|Transaction query()
 * @method static Builder|Transaction whereCreatedAt($value)
 * @method static Builder|Transaction whereDeletedAt($value)
 * @method static Builder|Transaction whereId($value)
 * @method static Builder|Transaction whereNote($value)
 * @method static Builder|Transaction wherePayableId($value)
 * @method static Builder|Transaction wherePayableType($value)
 * @method static Builder|Transaction wherePaymentSysId($value)
 * @method static Builder|Transaction wherePaymentTrxId($value)
 * @method static Builder|Transaction wherePerformTime($value)
 * @method static Builder|Transaction wherePrice($value)
 * @method static Builder|Transaction whereRefundTime($value)
 * @method static Builder|Transaction whereStatus($value)
 * @method static Builder|Transaction whereStatusDescription($value)
 * @method static Builder|Transaction whereUpdatedAt($value)
 * @method static Builder|Transaction whereUserId($value)
 * @mixin Eloquent
 */
class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const STATUS_PROGRESS   = 'progress';
    const STATUS_PAID       = 'paid';
    const STATUS_CANCELED   = 'canceled';
    const STATUS_REJECTED   = 'rejected';
    const STATUS_REFUND     = 'refund';

    const STATUSES = [
        self::STATUS_PROGRESS   => self::STATUS_PROGRESS,
        self::STATUS_PAID       => self::STATUS_PAID,
        self::STATUS_CANCELED   => self::STATUS_CANCELED,
        self::STATUS_REJECTED   => self::STATUS_REJECTED,
        self::STATUS_REFUND     => self::STATUS_REFUND,
    ];

    public function payable(): MorphTo
    {
        return $this->morphTo('payable');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function paymentSystem(): BelongsTo
    {
        return $this->belongsTo(Payment::class, 'payment_sys_id');
    }

    public function scopeFilter($query, $filter = [])
    {
        return $query
            ->when(data_get($filter, 'model') == 'orders' , function (Builder $query) {
                $query->where(['payable_type' => Order::class]);
            })
            ->when(data_get($filter, 'shop_id'), function (Builder $q, $shopId) {

                $q->whereHasMorph('payable', Order::class, function (Builder $b) use ($shopId) {
                    $b->where('shop_id', $shopId);
                });

            })
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->when(data_get($filter, 'model') == 'wallet', fn($q) => $q->where(['payable_type' => Wallet::class]))
            ->when(data_get($filter, 'user_id'), fn($q, $userId) => $q->where('user_id', $userId))
            ->when(data_get($filter, 'status'), fn($q, $status) => $q->where('status', $status));
    }
}
