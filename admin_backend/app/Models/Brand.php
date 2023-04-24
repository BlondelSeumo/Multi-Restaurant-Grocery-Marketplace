<?php

namespace App\Models;

use App\Traits\Loadable;
use App\Traits\MetaTagable;
use Database\Factories\BrandFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Brand
 *
 * @property int $id
 * @property string $uuid
 * @property string $title
 * @property int $active
 * @property string|null $img
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @property-read Collection|Product[] $products
 * @property-read int|null $products_count
 * @method static BrandFactory factory(...$parameters)
 * @method static Builder|Brand filter($array)
 * @method static Builder|Brand newModelQuery()
 * @method static Builder|Brand newQuery()
 * @method static Builder|Brand query()
 * @method static Builder|Brand updatedDate($updatedDate)
 * @method static Builder|Brand whereActive($value)
 * @method static Builder|Brand whereCreatedAt($value)
 * @method static Builder|Brand whereDeletedAt($value)
 * @method static Builder|Brand whereId($value)
 * @method static Builder|Brand whereImg($value)
 * @method static Builder|Brand whereTitle($value)
 * @method static Builder|Brand whereUpdatedAt($value)
 * @method static Builder|Brand whereUuid($value)
 * @mixin Eloquent
 */
class Brand extends Model
{
    use HasFactory, Loadable, MetaTagable, SoftDeletes;

    protected $guarded = ['id'];

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeUpdatedDate($query, $updatedDate)
    {
        return $query->where('updated_at', '>', $updatedDate);
    }

    /* Filter Scope */
    public function scopeFilter($value, $filter)
    {
        return $value
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->when(data_get($filter, 'search'), fn($q, $search) => $q->where('title', 'LIKE', "%$search%"))
            ->when(isset($filter['active']), function ($q) use ($filter) {
                $q->whereActive($filter['active']);
            })
            ->when(data_get($filter,'column'), function (Builder $query, $column) use($filter) {
                $query->orderBy($column, data_get($filter, 'sort', 'desc'));
            }, fn($query) => $query->orderBy('id', 'desc'));
    }

}
