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
 * App\Models\EmailSubscription
 *
 * @property int $id
 * @property string $user_id
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read User $user
 * @method static Builder|EmailSubscription newModelQuery()
 * @method static Builder|EmailSubscription newQuery()
 * @method static Builder|EmailSubscription query()
 * @method static Builder|EmailSubscription whereCreatedAt($value)
 * @method static Builder|EmailSubscription whereId($value)
 * @method static Builder|EmailSubscription whereUpdatedAt($value)
 * @method static Builder|EmailSubscription whereUserId($value)
 * @mixin Eloquent
 */
class EmailSubscription extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
