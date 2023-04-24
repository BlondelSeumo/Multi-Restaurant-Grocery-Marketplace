<?php

namespace App\Models;

use Database\Factories\ReviewFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\MetaTag
 *
 * @property int $id
 * @property string $path
 * @property int $model_id
 * @property string $model_type
 * @property string $title
 * @property string $keywords
 * @property string $description
 * @property string $h1
 * @property string $seo_text
 * @property string $canonical
 * @property string $robots
 * @property string $change_freq
 * @property string $priority
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static ReviewFactory factory(...$parameters)
 * @method static Builder|MetaTag newModelQuery()
 * @method static Builder|MetaTag newQuery()
 * @method static Builder|MetaTag query()
 * @method static Builder|MetaTag whereCreatedAt($value)
 * @method static Builder|MetaTag whereId($value)
 * @method static Builder|MetaTag whereModelId($value)
 * @method static Builder|MetaTag whereModelType($value)
 * @method static Builder|MetaTag whereUpdatedAt($value)
 * @mixin Eloquent
 */
class MetaTag extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = ['id'];

    public function model(): MorphTo
    {
        return $this->morphTo();
    }
}
