<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Translation
 *
 * @property int $id
 * @property int $status
 * @property string $locale
 * @property string $group
 * @property string $key
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|Translation filter($array = [])
 * @method static Builder|Translation newModelQuery()
 * @method static Builder|Translation newQuery()
 * @method static Builder|Translation query()
 * @method static Builder|Translation whereCreatedAt($value)
 * @method static Builder|Translation whereGroup($value)
 * @method static Builder|Translation whereId($value)
 * @method static Builder|Translation whereKey($value)
 * @method static Builder|Translation whereLocale($value)
 * @method static Builder|Translation whereStatus($value)
 * @method static Builder|Translation whereUpdatedAt($value)
 * @method static Builder|Translation whereValue($value)
 * @mixin Eloquent
 */
class Translation extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function scopeFilter($query, $array = [])
    {
        return $query->when(isset($array['group']), function ($q)  use ($array) {
            $q->where('group', $array['group']);
        })->when(isset($array['locale']), function ($q)  use ($array) {
            $q->where('locale', $array['locale']);
        })->when(isset($filter['deleted_at']), function ($q) {
            $q->onlyTrashed();
        });
    }
}
