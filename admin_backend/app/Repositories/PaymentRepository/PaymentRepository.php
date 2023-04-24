<?php

namespace App\Repositories\PaymentRepository;

use App\Models\Payment;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PaymentRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Payment::class;
    }

    public function paginate(array $filter): LengthAwarePaginator
    {
        /** @var Payment $payment */
        $payment = $this->model();

        return $payment
            ->when(data_get($filter, 'active'), function ($q, $active) {
                $q->where('active', $active);
            })
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->paginate(data_get($filter, 'perPage', 10));
    }

    public function paymentsList(array $filter): Collection|array
    {
        /** @var Payment $payment */
        $payment = $this->model();

        return $payment
            ->when(data_get($filter, 'active'), function ($q, $active) {
                $q->where('active', $active);
            })
            ->when(isset($filter['deleted_at']), fn($q) => $q->onlyTrashed())
            ->get();
    }

    public function paymentDetails(int $id)
    {
        return $this->model()->find($id);
    }
}
