<?php

namespace App\Models;

use Database\Factories\FaqTranslationFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * App\Models\FaqTranslation
 *
 * @property int $id
 * @property int $faq_id
 * @property string $locale
 * @property string $question
 * @property string|null $answer
 * @method static FaqTranslationFactory factory(...$parameters)
 * @method static Builder|FaqTranslation newModelQuery()
 * @method static Builder|FaqTranslation newQuery()
 * @method static Builder|FaqTranslation query()
 * @method static Builder|FaqTranslation whereAnswer($value)
 * @method static Builder|FaqTranslation whereFaqId($value)
 * @method static Builder|FaqTranslation whereId($value)
 * @method static Builder|FaqTranslation whereLocale($value)
 * @method static Builder|FaqTranslation whereQuestion($value)
 * @mixin Eloquent
 */
class FaqTranslation extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $guarded = ['id'];
}
