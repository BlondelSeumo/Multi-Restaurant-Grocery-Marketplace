<?php

namespace App\Repositories\ExtraRepository;

use App\Models\ExtraValue;
use App\Repositories\CoreRepository;

class ExtraValueRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return ExtraValue::class;
    }

    public function extraValueList($active = null, $group = null)
    {
        return $this->model()
            ->with([
                'group.translation' => fn($q) => $q->where('locale', $this->language)
            ])
            ->when(isset($active), fn($q) => $q->where('active', $active))
            ->when(isset($group), fn($q) => $q->where('extra_group_id', $group))
            ->when(request('deleted_at'), fn($q) => $q->onlyTrashed())
            ->orderBy('id', 'desc')
            ->get();
    }

    public function extraValueDetails(int $id)
    {
        return $this->model()
            ->with([
                'galleries'         => fn($q) => $q->select('id', 'type', 'loadable_id', 'path', 'title'),
                'group.translation' => fn($q) => $q->where('locale', $this->language)
            ])
            ->find($id);
    }

}
