<?php

namespace App\Repositories\ExtraRepository;

use App\Models\ExtraGroup;
use App\Repositories\CoreRepository;

class ExtraGroupRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return ExtraGroup::class;
    }

    public function extraGroupList(array $filter = [])
    {
        return $this->model()
            ->with([
                'translation' => fn($q) => $q
                    ->when(data_get($filter, 'search'), fn ($q, $search) => $q->where('title', 'LIKE', "%$search%"))
            ])
            ->whereHas('translation', fn($q) => $q
                ->when(data_get($filter, 'search'), fn ($q, $search) => $q->where('title', 'LIKE', "%$search%"))
            )
            ->when(data_get($filter, 'active'), fn($q, $active) => $q->where('active', $active))
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->get();
    }

    public function extraGroupDetails(int $id)
    {
        return $this->model()
            ->with([
                'translation' => fn($q) => $q->where('locale', $this->language)
            ])
            ->whereHas('translation', fn($q) => $q->where('locale', $this->language))
            ->find($id);
    }

}
