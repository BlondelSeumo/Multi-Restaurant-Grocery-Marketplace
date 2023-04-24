<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Settings
 *
 * @property int $id
 * @property string $key
 * @property string|null $value
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @method static Builder|Settings newModelQuery()
 * @method static Builder|Settings newQuery()
 * @method static Builder|Settings query()
 * @method static Builder|Settings whereCreatedAt($value)
 * @method static Builder|Settings whereId($value)
 * @method static Builder|Settings whereKey($value)
 * @method static Builder|Settings whereUpdatedAt($value)
 * @method static Builder|Settings whereValue($value)
 * @mixin Eloquent
 */
class Settings extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    const TTL = 2592000; // 30 days

    public static function adminSettings()
    {
        return Cache::remember('admin-settings', self::TTL, function () {
            return self::all();
        });
    }
}
