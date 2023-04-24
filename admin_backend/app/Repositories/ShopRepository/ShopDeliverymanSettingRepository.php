<?php

namespace App\Repositories\ShopRepository;

use App\Models\Language;
use App\Models\ShopDeliverymanSetting;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class ShopDeliverymanSettingRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return ShopDeliverymanSetting::class;
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginate(array $filter = []): LengthAwarePaginator
    {
        /** @var ShopDeliverymanSetting $models */
        $models = $this->model();
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $models
            ->filter($filter)
            ->with([
                'shopSection.translation' => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            ])
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param ShopDeliverymanSetting $model
     * @return ShopDeliverymanSetting|null
     */
    public function show(ShopDeliverymanSetting $model): ShopDeliverymanSetting|null
    {
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $model->loadMissing([
            'shopSection.translation'       => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'shopSection.shop.translation'  => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale)
        ]);
    }
}
