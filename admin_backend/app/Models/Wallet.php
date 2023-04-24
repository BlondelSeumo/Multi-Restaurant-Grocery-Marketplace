<?php

namespace App\Models;

use App\Traits\Payable;
use Database\Factories\WalletFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Wallet
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $currency_id
 * @property float $price
 * @property float $price_rate
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Currency $currency
 * @property-read mixed $symbol
 * @property-read Collection|WalletHistory[] $histories
 * @property-read int|null $histories_count
 * @property-read Collection|Transaction[] $transactions
 * @property-read int|null $transactions_count
 * @property-read User $user
 * @method static WalletFactory factory(...$parameters)
 * @method static Builder|Wallet newModelQuery()
 * @method static Builder|Wallet newQuery()
 * @method static Builder|Wallet onlyTrashed()
 * @method static Builder|Wallet query()
 * @method static Builder|Wallet whereCreatedAt($value)
 * @method static Builder|Wallet whereCurrencyId($value)
 * @method static Builder|Wallet whereDeletedAt($value)
 * @method static Builder|Wallet whereId($value)
 * @method static Builder|Wallet wherePrice($value)
 * @method static Builder|Wallet whereUpdatedAt($value)
 * @method static Builder|Wallet whereUserId($value)
 * @method static Builder|Wallet whereUuid($value)
 * @method static Builder|Wallet withTrashed()
 * @method static Builder|Wallet withoutTrashed()
 * @mixin Eloquent
 */
class Wallet extends Model
{
    use HasFactory, SoftDeletes, Payable;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(WalletHistory::class, 'wallet_uuid', 'uuid');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class);
    }

    public function getPriceRateAttribute(): float
    {
        $currency = request('currency_id')
            ? Currency::currenciesList()->where('id', request('currency_id'))->first()
            : Currency::currenciesList()->where('default', 1)->first();

        return $this->price * $currency?->rate;
    }

    public function getSymbolAttribute()
    {
        $currency = request('currency_id')
            ? Currency::currenciesList()->where('id', request('currency_id'))->first()
            : Currency::currenciesList()->where('default', 1)->first();

        return $currency->symbol;
    }
}
