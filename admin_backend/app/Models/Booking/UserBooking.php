<?php

namespace App\Models\Booking;

use App\Models\User;
use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\UserBooking
 *
 * @property int $id
 * @property int|null $booking_id
 * @property int|null $user_id
 * @property int|null $table_id
 * @property Carbon|null $start_date
 * @property Carbon|null $end_date
 * @property Carbon|null $deleted_at
 * @property Booking|null $booking
 * @property User|null $user
 * @property Table|null $table
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self filter($filter)
 * @mixin Eloquent
 */
class UserBooking extends Model
{
    protected $guarded = ['id'];
    public $timestamps = false;

    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function table(): BelongsTo
    {
        return $this->belongsTo(Table::class);
    }

    public function scopeFilter($query, $filter) {
        $query
            ->when(data_get($filter, 'booking_id'), fn($q, $bookingId) => $q->where('booking_id', $bookingId))
            ->when(data_get($filter, 'shop_id'), fn($q, $shopId) => $q->whereHas('booking', fn($b) => $b->where('shop_id', $shopId)))
            ->when(data_get($filter, 'user_id'), fn($q, $userId) => $q->where('user_id', $userId))
            ->when(data_get($filter, 'table_id'), fn($q, $tableId) => $q->where('table_id', $tableId))
            ->when(data_get($filter, 'start_date_from'), fn($q, $startFrom) => $q->where('start_date', '>=', $startFrom))
            ->when(data_get($filter, 'start_date_to'), fn($q, $startTo) => $q->where('start_date', '<=', $startTo))
            ->when(data_get($filter, 'end_date_from'), fn($q, $endFrom) => $q->where('end_date', '>=', $endFrom))
            ->when(data_get($filter, 'end_date_to'), fn($q, $endTo) => $q->where('end_date', '<=', $endTo));
    }
}
