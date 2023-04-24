<?php

namespace App\Models;

use App\Traits\Loadable;
use App\Traits\Reviewable;
use Database\Factories\BlogFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Blog
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $type
 * @property string|null $published_at
 * @property int $active
 * @property string|null $img
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @property-read Collection|Review[] $reviews
 * @property-read int|null $reviews_count
 * @property-read BlogTranslation|null $translation
 * @property-read Collection|BlogTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static BlogFactory factory(...$parameters)
 * @method static Builder|Blog newModelQuery()
 * @method static Builder|Blog newQuery()
 * @method static Builder|Blog query()
 * @method static Builder|Blog whereActive($value)
 * @method static Builder|Blog whereCreatedAt($value)
 * @method static Builder|Blog whereDeletedAt($value)
 * @method static Builder|Blog whereId($value)
 * @method static Builder|Blog whereImg($value)
 * @method static Builder|Blog wherePublishedAt($value)
 * @method static Builder|Blog whereType($value)
 * @method static Builder|Blog whereUpdatedAt($value)
 * @method static Builder|Blog whereUserId($value)
 * @method static Builder|Blog whereUuid($value)
 * @mixin Eloquent
 */
class Blog extends Model
{
    use HasFactory, Loadable, Reviewable, SoftDeletes;

    protected $guarded = ['id'];

    const TYPES = [
        'blog'          => 1,
        'notification'  => 2,
    ];

    public function getTypeAttribute($value)
    {
        return !is_null($value) ? data_get(self::TYPES, $value, 'blog') : 'blog';
    }

    public function translations(): HasMany
    {
        return $this->hasMany(BlogTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(BlogTranslation::class);
    }
}
