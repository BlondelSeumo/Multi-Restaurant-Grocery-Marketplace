<?php

namespace App\Models;

use App\Traits\Loadable;
use Carbon\Carbon;
use Database\Factories\ExtraValueFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\ExtraValue
 *
 * @property int $id
 * @property int $extra_group_id
 * @property string $value
 * @property int $active
 * @property Carbon|null $deleted_at
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @property-read ExtraGroup $group
 * @property-read Collection|Stock[] $stocks
 * @property-read int|null $stocks_count
 * @method static ExtraValueFactory factory(...$parameters)
 * @method static Builder|ExtraValue newModelQuery()
 * @method static Builder|ExtraValue newQuery()
 * @method static Builder|ExtraValue query()
 * @method static Builder|ExtraValue whereActive($value)
 * @method static Builder|ExtraValue whereExtraGroupId($value)
 * @method static Builder|ExtraValue whereId($value)
 * @method static Builder|ExtraValue whereValue($value)
 * @mixin Eloquent
 */
class ExtraValue extends Model
{
    use HasFactory, Loadable, SoftDeletes;

    protected $guarded = ['id'];

    public $timestamps = false;

    public function group(): BelongsTo
    {
        return $this->belongsTo(ExtraGroup::class, 'extra_group_id');
    }

    public function stocks(): BelongsToMany
    {
        return $this->belongsToMany(Stock::class, StockExtra::class);
    }
}
