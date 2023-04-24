<?php

namespace App\Repositories\Booking\BookingRepository;

use App\Models\Booking\Booking;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class BookingRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Booking::class;
    }

    public function paginate($filter = []): LengthAwarePaginator
    {
        /** @var Booking $models */
        $models = $this->model();

        return $models
            ->filter($filter)
            ->orderBy(data_get($filter, 'column', 'id'), data_get($filter, 'sort', 'desc'))
            ->paginate(data_get($filter, 'perPage', 10));
    }
}
