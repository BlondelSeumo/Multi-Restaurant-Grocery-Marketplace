<?php

namespace App\Models;

use App\Traits\Loadable;
use Carbon\Carbon;
use Database\Factories\LanguageFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\Language
 *
 * @property int $id
 * @property string|null $title
 * @property string $locale
 * @property int $backward
 * @property int $default
 * @property int $active
 * @property string|null $img
 * @property Carbon|null $deleted_at
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @method static LanguageFactory factory(...$parameters)
 * @method static Builder|Language newModelQuery()
 * @method static Builder|Language newQuery()
 * @method static Builder|Language query()
 * @method static Builder|Language whereActive($value)
 * @method static Builder|Language whereBackward($value)
 * @method static Builder|Language whereDefault($value)
 * @method static Builder|Language whereId($value)
 * @method static Builder|Language whereImg($value)
 * @method static Builder|Language whereLocale($value)
 * @method static Builder|Language whereTitle($value)
 * @mixin Eloquent
 */
class Language extends Model
{
    use HasFactory, Loadable, SoftDeletes;

    protected $guarded = [
        'id'
    ];

    public $timestamps = false;

    public static function languagesList() {
        return Cache::remember('languages-list', 84300, function () {
            return self::orderByDesc('id')->get();
        });
    }
}
