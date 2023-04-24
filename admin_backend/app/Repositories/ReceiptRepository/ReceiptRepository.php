<?php

namespace App\Repositories\ReceiptRepository;

use App\Models\Language;
use App\Models\Receipt;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ReceiptRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Receipt::class;
    }

    public function paginate($filter = []): LengthAwarePaginator
    {
        /** @var Receipt $models */
        $models = $this->model();
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $models
            ->filter($filter)
            ->with([
                'translation'  => fn($q) => $q->select('locale', 'title', 'receipt_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'ingredient'  => fn($q) => $q->select('locale', 'title', 'receipt_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'instruction'  => fn($q) => $q->select('locale', 'title', 'receipt_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'nutritions.translation'  => fn($q) => $q->select('locale', 'title', 'nutrition_id')
                    ->where('locale', $this->language),
                'shop:id,logo_img,uuid',
                'shop.translation'  => fn($q) => $q->select('locale', 'title', 'shop_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'category.translation'  => fn($q) => $q->select('locale', 'title', 'category_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'translations',
                'ingredients',
                'instructions',
                'nutritions.translations',
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function restPaginate($filter = []): LengthAwarePaginator
    {
        /** @var Receipt $models */
        $models = $this->model();
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $models
            ->filter($filter)
            ->with([
                'translation'  => fn($q) => $q->select('locale', 'title', 'receipt_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'ingredient'  => fn($q) => $q->select('locale', 'title', 'receipt_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'instruction'  => fn($q) => $q->select('locale', 'title', 'receipt_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'nutritions.translation'    => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
                'shop:id,logo_img,uuid',
                'shop.translation'  => fn($q) => $q->select('locale', 'title', 'shop_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'category.translation'  => fn($q) => $q->select('locale', 'title', 'category_id')
                    ->where('locale', $this->language)
                    ->orWhere('locale', $locale),
                'stocks',
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function show(Receipt $model): Receipt
    {
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $model->loadMissing([
            'galleries',
            'translation'               => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'ingredient'                => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'instruction'               => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'nutritions.translation'    => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'translations',
            'ingredients',
            'instructions',
            'nutritions.translations',
            'shop:id,logo_img,uuid',
            'shop.translation'  => fn($q) => $q->select('locale', 'title', 'shop_id')
                ->where('locale', $this->language)
                ->orWhere('locale', $locale),
            'category.translation'  => fn($q) => $q->select('locale', 'title', 'category_id')
                ->where('locale', $this->language)
                ->orWhere('locale', $locale),
            'stocks.countable:id,active,addon,img',
            'stocks.countable.translation' => fn($q) => $q->select('locale', 'title', 'product_id')
                ->where('locale', $this->language)
                ->orWhere('locale', $locale),
        ]);
    }

    public function restShow(Receipt $model): Receipt
    {
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $model->loadMissing([
            'galleries',
            'translation'               => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'ingredient'                => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'instruction'               => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'nutritions.translation'    => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'shop:id,logo_img,uuid',
            'shop.translation'  => fn($q) => $q->select('locale', 'title', 'shop_id')
                ->where('locale', $this->language)
                ->orWhere('locale', $locale),
            'category.translation'  => fn($q) => $q->select('locale', 'title', 'category_id')
                ->where('locale', $this->language)
                ->orWhere('locale', $locale),
            'stocks.countable:id,active,addon,img',
            'stocks.countable.translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
        ]);
    }
}
