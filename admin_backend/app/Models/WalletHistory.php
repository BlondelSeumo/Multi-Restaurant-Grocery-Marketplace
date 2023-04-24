<?php

namespace App\Models;

use App\Traits\SetCurrency;
use Database\Factories\WalletHistoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\WalletHistory
 *
 * @property int $id
 * @property string $uuid
 * @property string $wallet_uuid
 * @property int|null $transaction_id
 * @property string $type
 * @property float $price
 * @property float $price_rate
 * @property string|null $note
 * @property string $status
 * @property int $created_by
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $author
 * @property-read User|null $user
 * @property-read Wallet|null $wallet
 * @property-read Transaction|null $transaction
 * @method static WalletHistoryFactory factory(...$parameters)
 * @method static Builder|WalletHistory newModelQuery()
 * @method static Builder|WalletHistory newQuery()
 * @method static Builder|WalletHistory query()
 * @method static Builder|WalletHistory whereCreatedAt($value)
 * @method static Builder|WalletHistory whereCreatedBy($value)
 * @method static Builder|WalletHistory whereId($value)
 * @method static Builder|WalletHistory whereNote($value)
 * @method static Builder|WalletHistory wherePrice($value)
 * @method static Builder|WalletHistory whereStatus($value)
 * @method static Builder|WalletHistory whereTransactionId($value)
 * @method static Builder|WalletHistory whereType($value)
 * @method static Builder|WalletHistory whereUpdatedAt($value)
 * @method static Builder|WalletHistory whereUuid($value)
 * @method static Builder|WalletHistory whereWalletUuid($value)
 * @mixin Eloquent
 */
class WalletHistory extends Model
{
    use HasFactory, SetCurrency, SoftDeletes;

    protected $guarded = ['id'];

    const PROCESSED = 'processed';
    const PAID      = 'paid';
    const REJECTED  = 'rejected';
    const CANCELED  = 'canceled';

    const TYPES     = [
        'topup',
        'withdraw',
        'referral_from_topup',
        'referral_from_withdraw',
    ];

    const STATUTES = [
        self::PROCESSED => self::PROCESSED,
        self::PAID      => self::PAID,
        self::REJECTED  => self::REJECTED,
        self::CANCELED  => self::CANCELED,
    ];

    public function getPriceRateAttribute(): float
    {
        if (request()->is('api/v1/dashboard/user/*') || request()->is('api/v1/rest/*')) {
            return $this->price * $this->currency();
        }

        return $this->price * $this->currency();
    }

    public function wallet(): BelongsTo
    {
        return $this->belongsTo(Wallet::class, 'wallet_uuid', 'uuid');
    }

    public function transaction(): BelongsTo
    {
        return $this->belongsTo(Transaction::class);
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function user(): HasOneThrough
    {
        return $this->hasOneThrough(User::class, Wallet::class,
            'uuid', 'id', 'wallet_uuid', 'user_id');
    }
}
