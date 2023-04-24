<?php

namespace App\Repositories\PayoutsRepository;

use App\Models\Payout;
use App\Repositories\CoreRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class PayoutsRepository extends CoreRepository
{
    protected function getModelClass(): string
    {
        return Payout::class;
    }

    /**
     * @param array $filter
     * @return LengthAwarePaginator
     */
    public function paginate(array $filter = []): LengthAwarePaginator
    {
        return Payout::filter($filter)->with([
            'currency',
            'payment',
            'createdBy:id,uuid,firstname,lastname,img',
            'createdBy.wallet',
            'approvedBy:id,uuid,firstname,lastname,img',
            'approvedBy.wallet',
        ])->paginate(data_get($filter, 'perPage', 10));
    }

    /**
     * @param Payout $payout
     * @return Payout
     */
    public function show(Payout $payout): Payout
    {
        return $payout->load([
            'currency',
            'payment',
            'createdBy:id,uuid,firstname,lastname,img',
            'createdBy.wallet',
            'approvedBy:id,uuid,firstname,lastname,img',
            'approvedBy.wallet',
        ]);
    }
}
