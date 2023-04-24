<?php

namespace App\Models;

use Carbon\Carbon;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\UserPoint
 *
 * @property int $id
 * @property int $user_id
 * @property float $price
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @method static Builder|UserPoint newModelQuery()
 * @method static Builder|UserPoint newQuery()
 * @method static Builder|UserPoint query()
 * @method static Builder|UserPoint whereId($value)
 * @method static Builder|UserPoint wherePrice($value)
 * @method static Builder|UserPoint whereUserId($value)
 * @mixin Eloquent
 */
class UserPoint extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
