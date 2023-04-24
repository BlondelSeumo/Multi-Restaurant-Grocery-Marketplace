<?php

namespace App\Repositories\Booking\TableRepository;

use App\Models\Booking\Table;
use App\Models\Language;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class TableRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Table::class;
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginate(array $filter = []): LengthAwarePaginator
    {
        /** @var Table $models */
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
     * @param Table $model
     * @return Table|null
     */
    public function show(Table $model): Table|null
    {
        $locale = data_get(Language::languagesList()->where('default', 1)->first(), 'locale');

        return $model->loadMissing([
            'shopSection.translation'       => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale),
            'shopSection.shop.translation'  => fn($q) => $q->where('locale', $this->language)->orWhere('locale', $locale)
        ]);
    }
}
