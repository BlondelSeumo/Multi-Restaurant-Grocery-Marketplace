<?php

namespace App\Repositories\PaymentPayloadRepository;

use App\Models\PaymentPayload;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PaymentPayloadRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return PaymentPayload::class;
    }

    /**
     * @param array $data
     * @return LengthAwarePaginator
     */
    public function paginate(array $data = []): LengthAwarePaginator
    {
        return $this->model()
            ->with('payment')
            ->when(isset($data['deleted_at']), fn($q) => $q->onlyTrashed())
            ->paginate(data_get($data, 'perPage', 10));
    }

    /**
     * @param int $paymentId
     * @return Builder|Model|null
     */
    public function show(int $paymentId): Builder|Model|null
    {
        return $this->model()->with('payment')->where('payment_id', $paymentId)->first();
    }
}
