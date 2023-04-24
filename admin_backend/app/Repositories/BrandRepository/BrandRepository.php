<?php

namespace App\Repositories\BrandRepository;

use App\Models\Brand;
use App\Repositories\CoreRepository;

class BrandRepository extends CoreRepository
{
    /**
     * @return string
     */
    protected function getModelClass(): string
    {
        return Brand::class;
    }

    public function brandsList(array $array = [])
    {
        return $this->model()
            ->updatedDate($this->updatedDate)
            ->filter($array)
            ->orderByDesc('id')
            ->get();
    }

    /**
     * Get brands with pagination
     */
    public function brandsPaginate(array $filter = [])
    {
        return $this->model()
            ->withCount([
                'products' => fn($q) => $q->whereHas('shop', fn($q) => $q->whereNull('deleted_at') )
                    ->whereHas('stocks', fn($q) => $q->where('quantity', '>', 0))
            ])
            ->filter($filter)
            ->updatedDate($this->updatedDate)
            ->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * Get one brands by Identification number
     */
    public function brandDetails(int $id)
    {
        return $this->model()->find($id);
    }

    public function brandsSearch(?string $search = null, $active = null)
    {
        return $this->model()
            ->withCount('products')
            ->where('title', 'LIKE', "%$search%")
            ->when(isset($active), function ($q) use ($active) {
                $q->whereActive($active);
            })
            ->latest()
            ->take(50)
            ->get();
    }
}
