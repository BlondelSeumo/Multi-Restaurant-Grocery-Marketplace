<?php

namespace App\Repositories\Booking\ShopSectionRepository;

use App\Models\Booking\ShopSection;
use App\Models\Language;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShopSectionRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return ShopSection::class;
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginate(array $filter = []): LengthAwarePaginator
    {
        /** @var ShopSection $models */
        $models = $this->model();
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $models
            ->filter($filter)
            ->with([
                'gallery',
                'translations',
                'translation'       => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
                'shop.translation'  => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale)
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param ShopSection $model
     * @return ShopSection|null
     */
    public function show(ShopSection $model): ShopSection|null
    {
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $model
            ->loadMissing([
                'galleries',
                'translations',
                'translation'       => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
                'shop.translation'  => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale)
            ]);
    }
}
