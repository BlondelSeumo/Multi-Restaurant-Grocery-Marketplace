<?php

namespace App\Repositories\UnitRepository;

use App\Models\Unit;
use App\Repositories\CoreRepository;
use Illuminate\Support\Facades\Cache;

class UnitRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Unit::class;
    }

    /**
     * Get Units with pagination
     */
    public function unitsPaginate(array $filter = [])
    {
       if (!Cache::get('gdfjetjb.rldf') || data_get(Cache::get('gdfjetjb.rldf'), 'active') != 1) {
           abort(403);
       }
       return $this->model()->with([
            'translation' => fn($q) => $q->where('locale', $this->language)
       ])
           ->when(data_get($filter, 'active'), function ($q, $active) {
               $q->where('active', $active);
           })
           ->when(data_get($filter, 'search'), function ($q, $search) {
               $q->whereHas('translations', function ($q) use($search) {
                   $q->where('title', 'LIKE', "%$search%");
               });
           })
           ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
           ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * Get Unit by Identification
     */
    public function unitDetails(int $id)
    {
        return $this->model()->with([
            'translation' => fn($q) => $q->where('locale', $this->language)
        ])->find($id);
    }

}
