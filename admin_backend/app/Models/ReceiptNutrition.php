<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\ReceiptNutrition
 *
 * @property int $id
 * @property int $receipt_id
 * @property Receipt|null $receipt
 * @property Collection|Translation[] $translations
 * @property Translation|null $translation
 * @property ReceiptTranslation|null $translations_count
 * @property string $weight
 * @property int $percentage
 * @property Carbon|null $deleted_at
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self whereId($value)
 * @mixin Eloquent
 */
class ReceiptNutrition extends Model
{
    use SoftDeletes;

    public $timestamps = false;

    protected $table = 'receipt_nutritions';

    protected $guarded = ['id'];

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ReceiptNutritionTranslation::class, 'nutrition_id');
    }

    public function translation(): HasOne
    {
        return $this->hasOne(ReceiptNutritionTranslation::class, 'nutrition_id');
    }
}
