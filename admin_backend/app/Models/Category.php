<?php

namespace App\Models;

use App\Traits\Loadable;
use App\Traits\MetaTagable;
use Database\Factories\CategoryFactory;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

/**
 * App\Models\Category
 *
 * @property int $id
 * @property string $uuid
 * @property string|null $keywords
 * @property int|null $parent_id
 * @property int $type
 * @property string|null $img
 * @property int $active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property-read Collection|Category[] $children
 * @property-read int|null $children_count
 * @property-read Collection|Gallery[] $galleries
 * @property-read int|null $galleries_count
 * @property-read Category|null $parent
 * @property-read Collection|Product[] $products
 * @property-read Collection|Stock[] $stocks
 * @property-read int|null $products_count
 * @property-read int|null $stocks_count
 * @property-read CategoryTranslation|null $translation
 * @property-read Collection|CategoryTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static CategoryFactory factory(...$parameters)
 * @method static Builder|Category filter($array)
 * @method static Builder|Category withThreeChildren($array)
 * @method static Builder|Category withTrashedThreeChildren($array)
 * @method static Builder|Category withSecondChildren($array)
 * @method static Builder|Category withParent($array)
 * @method static Builder|Category newModelQuery()
 * @method static Builder|Category newQuery()
 * @method static Builder|Category onlyTrashed()
 * @method static Builder|Category query()
 * @method static Builder|Category updatedDate($updatedDate)
 * @method static Builder|Category whereActive($value)
 * @method static Builder|Category whereCreatedAt($value)
 * @method static Builder|Category whereDeletedAt($value)
 * @method static Builder|Category whereId($value)
 * @method static Builder|Category whereImg($value)
 * @method static Builder|Category whereKeywords($value)
 * @method static Builder|Category whereParentId($value)
 * @method static Builder|Category whereType($value)
 * @method static Builder|Category whereUpdatedAt($value)
 * @method static Builder|Category whereUuid($value)
 * @method static Builder|Category withTrashed()
 * @method static Builder|Category withoutTrashed()
 * @mixin Eloquent
 */
class Category extends Model
{
    use HasFactory, Loadable, SoftDeletes, MetaTagable;

    protected $guarded = ['id'];

    const MAIN      = 1;
    const BLOG      = 2;
    const BRAND     = 3;
    const SHOP      = 4;
    const RECEIPT   = 5;

    const TYPES = [
        'main'    => self::MAIN,
        'blog'    => self::BLOG,
        'brand'   => self::BRAND,
        'shop'    => self::SHOP,
        'receipt' => self::RECEIPT,
    ];

    const TYPES_VALUES = [
        self::MAIN      => 'main',
        self::BLOG      => 'blog',
        self::BRAND     => 'brand',
        self::SHOP      => 'shop',
        self::RECEIPT   => 'receipt',
    ];

    protected $casts = [
        'active'     => 'bool',
        'created_at' => 'datetime:Y-m-d H:i:s',
        'updated_at' => 'datetime:Y-m-d H:i:s',
    ];

    public function getTypeAttribute($value)
    {
        return !is_null($value) ? data_get(self::TYPES, $value) : 'main';
    }

    public function translations(): HasMany
    {
        return $this->hasMany(CategoryTranslation::class);
    }

    public function translation(): HasOne
    {
        return $this->hasOne(CategoryTranslation::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function stocks(): HasManyThrough
    {
        return $this
            ->hasManyThrough(Stock::class, Product::class, 'category_id', 'countable_id')
            ->where('countable_type', Product::class);
    }

    public function shopCategory(): HasMany
    {
        return $this->hasMany(ShopCategory::class);
    }

    public function scopeUpdatedDate($query, $updatedDate)
    {
        $query->where('updated_at', '>', $updatedDate);
    }

    public function scopeWithSecondChildren($query, $data)
    {
        $query->with([
            'translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),

            'children' => fn($q) => $q->select(['id', 'uuid', 'keywords', 'parent_id', 'type', 'img', 'active']),

            'children.translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),
        ]);
    }

    public function scopeWithParent($query, $data)
    {
        $query->with([
            'translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),

            'parent' => fn($q) => $q->select(['id', 'uuid', 'keywords', 'parent_id', 'type', 'img', 'active']),

            'parent.translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),
        ]);
    }

    public function scopeWithThreeChildren($query, $data)
    {
        $query->with([
            'translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),

            'children' => fn($q) => $q->select(['id', 'uuid', 'keywords', 'parent_id', 'type', 'img', 'active']),

            'children.translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),

            'children.children' => fn($q) => $q->select([
                'id', 'uuid', 'keywords', 'parent_id', 'type', 'img', 'active'
            ]),

            'children.children.translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),
        ]);
    }

    public function scopeWithTrashedThreeChildren($query, $data)
    {
        $with = [
            'translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),

            'children' => fn($q) => $q->withTrashed()->select(['id', 'uuid', 'keywords', 'parent_id', 'type', 'img', 'active']),

            'children.translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),

            'children.children' => fn($q) => $q->withTrashed()->select([
                'id', 'uuid', 'keywords', 'parent_id', 'type', 'img', 'active'
            ]),

            'children.children.translation' => fn($q) => $q->select('id', 'locale', 'title', 'category_id')
                ->where('locale', data_get($data, 'language')),
        ];

        if (data_get($data, 'with')) {
            $with += data_get($data, 'with');
        }

        $query->with($with);
    }

    /* Filter Scope */
    public function scopeFilter($value, $array)
    {
        return $value
            ->when(in_array(data_get($array, 'type'), array_keys(Category::TYPES)), function ($q) use ($array) {
                $q->where('type', '=', data_get(Category::TYPES, $array['type'],Category::MAIN));
            })
            ->when(isset($array['active']), function ($q) use ($array) {
                $q->whereActive($array['active']);
            })
            ->when(isset($array['length']), function ($q) use ($array) {
                $q->skip(data_get($array, 'start', 0))->take($array['length']);
            })
            ->when(isset($array['shop_id']), fn($query) => $query
                ->whereHas('shopCategory', fn($q) => $q->where('shop_id', $array['shop_id']) )
            )
            ->when(isset($array['deleted_at']), fn($q) => $q->onlyTrashed())
            ->when(data_get($array, 'search'), function ($query, $search) {
                $query->where(function ($q) use($search) {
                    $q->where('keywords', 'LIKE', '%' . $search . '%')
                        ->orWhereHas('translation', function ($q) use ($search) {

                            $q->where('title', 'LIKE', '%' . $search . '%')
                                ->orWhere('keywords', 'LIKE', '%' . $search . '%');

                        })->orWhereHas('children.translation', function ($q) use ($search) {

                            $q->where('title', 'LIKE', '%' . $search . '%')
                                ->orWhere('keywords', 'LIKE', '%' . $search . '%');

                        })->orWhereHas('children.children.translation', function ($q) use ($search) {

                            $q->where('title', 'LIKE', '%' . $search . '%')
                                ->orWhere('keywords', 'LIKE', '%' . $search . '%');
                        });
                });
            });
    }
}
