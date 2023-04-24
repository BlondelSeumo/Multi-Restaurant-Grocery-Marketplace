<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Http\Requests\FilterParamsRequest;
use App\Http\Resources\OrderStatusResource;
use App\Models\OrderStatus;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Collection;

class OrderStatusController extends RestBaseController
{
    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return AnonymousResourceCollection
     */
    public function index(FilterParamsRequest $request): AnonymousResourceCollection
    {
        $orderStatuses = OrderStatus::list()->when($request->input('sort'), fn(Collection $q) =>
                $q->sortBy('sort', SORT_REGULAR, !($request->input('sort') === 'asc'))
            )
            ->where('active', '=', 1)
            ->all();

        return OrderStatusResource::collection($orderStatuses);
    }

    /**
     * Display a listing of the resource.
     */
    public function select(): array
    {
        return OrderStatus::listNames();
    }
}
