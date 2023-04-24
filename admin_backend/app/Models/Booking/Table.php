<?php

namespace App\Models\Booking;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Table
 *
 * @property int $id
 * @property int $name
 * @property int $shop_section_id
 * @property int $tax
 * @property int $chair_count
 * @property ShopSection|null $shopSection
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self filter(array $filter)
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereDeletedAt($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Table extends Model
{
    use SoftDeletes;

    protected $guarded = ['id'];

    public function shopSection(): BelongsTo
    {
        return $this->belongsTo(ShopSection::class);
    }

    public function scopeFilter($query, $filter) {

    }
}
