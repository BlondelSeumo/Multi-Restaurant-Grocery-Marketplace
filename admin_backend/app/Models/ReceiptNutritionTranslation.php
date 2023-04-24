<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\ReceiptNutritionTranslation
 *
 * @property int $id
 * @property string $locale
 * @property int $receipt_nutrition_id
 * @property ReceiptNutrition|null $receiptNutrition
 * @property string $title
 * @property Carbon|null $deleted_at
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ReceiptNutritionTranslation extends Model
{
    use SoftDeletes;

    public $timestamps = false;

//    protected $table = 'receipt_nutrition_translations';

    protected $guarded = ['id'];

    public function receiptNutrition(): BelongsTo
    {
        return $this->belongsTo(ReceiptNutrition::class);
    }
}
