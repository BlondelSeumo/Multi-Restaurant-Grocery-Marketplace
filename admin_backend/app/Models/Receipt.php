<?php

namespace App\Models;

use App\Traits\Loadable;
use App\Traits\SetCurrency;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

/**
 * App\Models\Receipt
 *
 * @property int $id
 * @property int $shop_id
 * @property string $img
 * @property string $discount_type
 * @property int $discount_price
 * @property int $category_id
 * @property string $active_time
 * @property string $total_time
 * @property int $calories
 * @property int $servings
 * @property Shop|null $shop
 * @property Collection|Stock[] $stocks
 * @property int $stocks_count
 * @property Collection|ReceiptTranslation[] $translations
 * @property ReceiptTranslation|null $translation
 * @property int $translations_count
 * @property Collection|ReceiptIngredient[] $ingredients
 * @property ReceiptIngredient|null $ingredient
 * @property int $ingredients_count
 * @property Collection|ReceiptInstruction[] $instructions
 * @property ReceiptInstruction|null $instruction
 * @property int $instructions_count
 * @property Collection|ReceiptNutrition[] $nutritions
 * @property int $nutritions_count
 * @property Collection|Gallery[] $galleries
 * @property int $galleries_count
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self query()
 * @method static Builder|self filter($filter)
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Receipt extends Model
{
    use SoftDeletes, Loadable, SetCurrency;

    protected $guarded = ['id'];

    public const DISCOUNT_TYPE_FIX     = 'fix';
    public const DISCOUNT_TYPE_PERCENT = 'percent';

    public const DISCOUNT_TYPES = [
        self::DISCOUNT_TYPE_FIX     => 0,
        self::DISCOUNT_TYPE_PERCENT => 1
    ];

    public const DISCOUNT_TYPE_VALUES = [
        0   => self::DISCOUNT_TYPE_FIX,
        1   => self::DISCOUNT_TYPE_PERCENT
    ];

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function stocks(): BelongsToMany
    {
        return $this->belongsToMany(Stock::class, ReceiptStock::class)->withPivot('min_quantity');
    }

    public function translations(): HasMany
    {
        return $this->hasMany(ReceiptTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(ReceiptTranslation::class);
    }

    public function instructions(): HasMany
    {
        return $this->hasMany(ReceiptInstruction::class);
    }

    public function instruction(): HasOne
    {
        return $this->hasOne(ReceiptInstruction::class);
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(ReceiptIngredient::class);
    }

    public function ingredient(): HasOne
    {
        return $this->hasOne(ReceiptIngredient::class);
    }

    public function nutritions(): HasMany
    {
        return $this->hasMany(ReceiptNutrition::class);
    }

    public function scopeFilter($query, $filter) {
        $query
            ->when(data_get($filter, 'shop_id'), fn($q, $shopId) => $q->where('shop_id', $shopId))
            ->when(data_get($filter, 'category_id'), fn($q, $categoryId) => $q->where('category_id', $categoryId))
            ->when(data_get($filter, 'discount_type'), fn($q, $discountType) => $q->where('discount_type', $discountType))
            ->when(data_get($filter, 'discount_price_from'), fn($q, $discountPriceFrom) => $q->where('discount_price', '>=', (int)$discountPriceFrom))
            ->when(data_get($filter, 'discount_price_to'), fn($q, $discountPriceTo) => $q->where('discount_price', '<=', (int)$discountPriceTo))
            ->when(data_get($filter, 'active_time_from'), fn($q, $activeTimeFrom) => $q->where('active_time', '>=', (int)$activeTimeFrom))
            ->when(data_get($filter, 'active_time_to'), fn($q, $activeTimeTo) => $q->where('active_time', '<=', (int)$activeTimeTo))
            ->when(data_get($filter, 'total_time_from'), fn($q, $totalTimeFrom) => $q->where('total_time', '>=', (int)$totalTimeFrom))
            ->when(data_get($filter, 'total_time_to'), fn($q, $totalTimeTo) => $q->where('total_time', '<=', (int)$totalTimeTo))
            ->when(data_get($filter, 'calories_from'), fn($q, $caloriesFrom) => $q->where('calories', '>=', (int)$caloriesFrom))
            ->when(data_get($filter, 'calories_to'), fn($q, $caloriesTo) => $q->where('calories', '<=', (int)$caloriesTo))
            ->when(data_get($filter, 'servings_from'), fn($q, $servingsFrom) => $q->where('servings', '>=', (int)$servingsFrom))
            ->when(data_get($filter, 'servings_to'), fn($q, $servingsTo) => $q->where('servings', '<=', (int)$servingsTo))
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->when(data_get($filter, 'search'), function ($q, $search) {
                $q->where(function ($query) use ($search) {
                    $query
                        ->whereHas('translation', function ($q) use($search) {
                            $q->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
                                ->where('title', 'LIKE', "%$search%")
                                ->select('id', 'receipt_id', 'locale', 'title');
                        })
                        ->orWhereHas('instruction', function ($q) use($search) {
                            $q->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
                                ->where('title', 'LIKE', "%$search%")
                                ->select('id', 'receipt_id', 'locale', 'title');
                        })
                        ->orWhereHas('ingredient', function ($q) use($search) {
                            $q->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
                                ->where('title', 'LIKE', "%$search%")
                                ->select('id', 'receipt_id', 'locale', 'title');
                        })
                        ->orWhereHas('nutritions', function ($q) use($search) {
                            $q
                                ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
                                ->where(function ($b) use ($search) {
                                    $b->where('weight', 'LIKE', "%$search%")
                                        ->orWhere('percentage', 'LIKE', "%$search%")
                                        ->orWhereHas('translation', function ($q) use($search) {
                                            $q->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
                                                ->where('title', 'LIKE', "%$search%")
                                                ->select('id', 'receipt_nutrition_id', 'locale', 'title');
                                        });
                                })
                                ->select('id', 'receipt_id', 'weight', 'percentage');
                        });
                });
            })
        ;
    }
}
