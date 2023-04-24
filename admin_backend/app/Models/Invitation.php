<?php

namespace App\Models;

use Database\Factories\InvitationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Invitation
 *
 * @property int $id
 * @property int $shop_id
 * @property int $user_id
 * @property string|null $role
 * @property int $status
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Shop $shop
 * @property-read User $user
 * @method static InvitationFactory factory(...$parameters)
 * @method static Builder|Invitation filter($array)
 * @method static Builder|Invitation newModelQuery()
 * @method static Builder|Invitation newQuery()
 * @method static Builder|Invitation query()
 * @method static Builder|Invitation whereCreatedAt($value)
 * @method static Builder|Invitation whereId($value)
 * @method static Builder|Invitation whereRole($value)
 * @method static Builder|Invitation whereShopId($value)
 * @method static Builder|Invitation whereStatus($value)
 * @method static Builder|Invitation whereUpdatedAt($value)
 * @method static Builder|Invitation whereUserId($value)
 * @mixin Eloquent
 */
class Invitation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const STATUS = [
        'new'       => 1,
        'viewed'    => 2,
        'excepted'  => 3,
        'rejected'  => 4
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function getStatusKey($value)
    {
        foreach (self::STATUS as $index => $status) {
            if ($value == $status) {
                return $index;
            }
        }
    }

    public function scopeFilter($query, $array)
    {
        $query->when(isset($array['user_id']), function ($q) use($array) {
            $q->where('user_id', $array['user_id']);
        })->when(isset($array['shop_id']), function ($q) use($array) {
            $q->where('shop_id', $array['shop_id']);
        });
    }
}
