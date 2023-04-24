<?php

namespace App\Models;

use Database\Factories\FaqFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;

/**
 * App\Models\EmailSetting
 *
 * @property int $id
 * @property boolean $smtp_auth
 * @property boolean $smtp_debug
 * @property string $host
 * @property int $port
 * @property string $password
 * @property string $from_to
 * @property string $from_site
 * @property array $ssl
 * @property boolean $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read FaqTranslation|null $translation
 * @property-read Collection|FaqTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static FaqFactory factory(...$parameters)
 * @method static Builder|Faq newModelQuery()
 * @method static Builder|Faq newQuery()
 * @method static Builder|Faq query()
 * @method static Builder|Faq whereActive($value)
 * @method static Builder|Faq whereCreatedAt($value)
 * @method static Builder|Faq whereId($value)
 * @method static Builder|Faq whereType($value)
 * @method static Builder|Faq whereUpdatedAt($value)
 * @method static Builder|Faq whereUuid($value)
 * @mixin Eloquent
 */
class EmailSetting extends Model
{
    use HasFactory, SoftDeletes;

    const TTL = 8640000000; // 100000 day

    protected $guarded = ['id'];

    protected $casts = [
        'ssl' => 'array',
    ];

    /**
     * @return mixed
     */
    public static function list(): mixed
    {
        return Cache::remember('email-settings-list', self::TTL, function () {
            return self::orderByDesc('id')->get();
        });
    }
}
