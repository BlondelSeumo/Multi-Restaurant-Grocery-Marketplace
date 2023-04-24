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
 * App\Models\ActiveReferral
 *
 * @property int $id
 * @property int $referral_id
 * @property int $from_id
 * @property int $to_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Referral|null $referral
 * @property-read User|null $from
 * @property-read User|null $to
 * @method static Builder|ActiveReferral newModelQuery()
 * @method static Builder|ActiveReferral newQuery()
 * @method static Builder|ActiveReferral query()
 * @method static Builder|ActiveReferral whereCreatedAt($value)
 * @method static Builder|ActiveReferral whereUpdatedAt($value)
 * @method static Builder|ActiveReferral whereDeletedAt($value)
 * @method static Builder|ActiveReferral whereId($value)
 * @method static Builder|ActiveReferral whereReferralId($value)
 * @method static Builder|ActiveReferral whereFromId($value)
 * @method static Builder|ActiveReferral whereToId($value)
 * @mixin Eloquent
 */
class ActiveReferral extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    /**
     * @return BelongsTo
     */
    public function referral(): BelongsTo
    {
        return $this->belongsTo(Referral::class);
    }

    /**
     * @return BelongsTo
     */
    public function from(): BelongsTo
    {
        return $this->belongsTo(User::class, 'from_id');
    }

    /**
     * @return BelongsTo
     */
    public function to(): BelongsTo
    {
        return $this->belongsTo(User::class, 'to_id');
    }

}
