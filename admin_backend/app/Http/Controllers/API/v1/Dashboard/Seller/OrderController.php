<?php

namespace App\Http\Controllers\API\v1\Dashboard\Seller;

use App\Exports\OrderExport;
use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Order\DeliveryManUpdateRequest;
use App\Http\Requests\Order\StocksCalculateRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Http\Resources\OrderResource;
use App\Imports\OrderImport;
use App\Models\Order;
use App\Models\Settings;
use App\Models\User;
use App\Repositories\DashboardRepository\DashboardRepository;
use App\Repositories\Interfaces\OrderRepoInterface;
use App\Repositories\OrderRepository\AdminOrderRepository;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\OrderService\OrderStatusUpdateService;
use App\Traits\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class OrderController extends SellerBaseController
{
    use Notification;

    public function __construct(
        private OrderRepoInterface $orderRepository, // todo remove
        private AdminOrderRepository $adminRepository,
        private OrderServiceInterface $orderService
    )
    {
        parent::__construct();
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function paginate(FilterParamsRequest $request): JsonResponse
    {
        $filter = $request->merge(['shop_id' => $this->shop->id])->all();

        $orders = $this->adminRepository->ordersPaginate($filter);
        $filter['date_from'] = date('Y-m-d H:i:s', strtotime('-1 minute'));
        $statistic = (new DashboardRepository)->orderByStatusStatistics($filter);

        $lastPage = (new DashboardRepository)->getLastPage(
            data_get($filter, 'perPage', 10),
            $statistic,
            data_get($filter, 'status')
        );

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), [
            'statistic' => $statistic,
            'orders'    =>  OrderResource::collection($orders),
            'meta'      => [
                'current_page'  => (int)data_get($filter, 'page', 1),
                'per_page'      => (int)data_get($filter, 'perPage', 10),
                'last_page'     => $lastPage
            ],
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param StoreRequest $request
     * @return JsonResponse
     */
    public function store(StoreRequest $request): JsonResponse
    {
        $validated = $request->validated();

        if ((int)data_get(Settings::adminSettings()->where('key', 'order_auto_approved')->first(), 'value') === 1) {
            $validated['status'] = Order::STATUS_ACCEPTED;
        }

        $result = $this->orderService->create($validated);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        $this->sendNotification(
            $this->tokens(),
            "New order was created",
            data_get($result, 'data.id', ''),
            data_get($result, 'data')?->setAttribute('type', 'new_order')?->only(['id', 'status'])
        );

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            $this->orderRepository->reDataOrder(data_get($result, 'data')),
        );
    }

    public function tokens(): array
    {
        $admins = User::with([
            'roles' => fn($q) => $q->where('name', 'admin')
        ])
            ->whereHas('roles', fn($q) => $q->where('name', 'admin') )
            ->whereNotNull('firebase_token')
            ->pluck('firebase_token')
            ->toArray();

        $aTokens = [];

        foreach ($admins as $adminToken) {
            $aTokens = array_merge($aTokens, is_array($adminToken) ? array_values($adminToken) : [$adminToken]);
        }

        return array_values(array_unique($aTokens));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderRepository->orderById($id, $this->shop->id);

        if ($order) {
            return $this->successResponse(
                __('errors.' . ResponseError::SUCCESS, locale: $this->language),
                $this->orderRepository->reDataOrder($order)
            );
        }

        return $this->onErrorResponse([
            'code'    => ResponseError::ERROR_404,
            'message' => __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param int $id
     * @param UpdateRequest $request
     * @return JsonResponse
     */
    public function update(int $id, UpdateRequest $request): JsonResponse
    {
        $result = $this->orderService->update($id, $request->all());

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            $this->orderRepository->reDataOrder(data_get($result, 'data')),
        );
    }

    /**
     * Update Order Status details by OrderDetail ID.
     *
     * @param int $orderId
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function orderStatusUpdate(int $orderId, FilterParamsRequest $request): JsonResponse
    {
        /** @var Order $order */
        $order = Order::with([
            'shop.seller',
            'deliveryMan',
            'user.wallet',
        ])
            ->where('shop_id', $this->shop->id)
            ->find($orderId);

        if (!$order) {
            return $this->onErrorResponse([
                'code' => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ]);
        }

        $result = (new OrderStatusUpdateService)->statusUpdate($order, $request->input('status'));

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::NO_ERROR),
            $this->orderRepository->reDataOrder(data_get($result, 'data')),
        );
    }

    /**
     * Update Order DeliveryMan Update.
     *
     * @param int $orderId
     * @param DeliveryManUpdateRequest $request
     * @return JsonResponse
     */
    public function orderDeliverymanUpdate(int $orderId, DeliveryManUpdateRequest $request): JsonResponse
    {
        $result = $this->orderService->updateDeliveryMan($orderId, $request->input('deliveryman'), $this->shop->id);

        if (!data_get($result, 'status')) {
            return $this->onErrorResponse($result);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_UPDATED, locale: $this->language),
            $this->orderRepository->reDataOrder(data_get($result, 'data')),
        );
    }

    /**
     * Calculate products when cart updated.
     *
     * @param StocksCalculateRequest $request
     * @return JsonResponse
     */
    public function orderStocksCalculate(StocksCalculateRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $validated['shop_id'] = $this->shop->id;

        $result = $this->orderRepository->orderStocksCalculate($validated);

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $result);
    }

    /**
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function destroy(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->orderService->destroy($request->input('ids'), $this->shop->id);

        if (count($result) > 0) {
            return $this->onErrorResponse([
                'code' => ResponseError::ERROR_400,
                'message' => __('errors.' . ResponseError::CANT_DELETE_ORDERS, [
                    'ids' => implode(', #', $result)
                ], locale: $this->language)
            ]);

        }
        return $this->successResponse('Successfully data');
    }

    public function fileExport(FilterParamsRequest $request): JsonResponse
    {
        $fileName = 'export/orders.xls';

        try {
            $filter = $request->merge(['shop_id' => $this->shop->id, 'language' => $this->language])->all();

            Excel::store(new OrderExport($filter), $fileName, 'public');

            return $this->successResponse('Successfully exported', [
                'path' => 'public/export',
                'file_name' => $fileName
            ]);
        } catch (Throwable $e) {
            $this->error($e);
            return $this->errorResponse('Error during export');
        }
    }

    public function fileImport(Request $request): JsonResponse
    {
        try {
            Excel::import(new OrderImport($this->language, $this->shop->id), $request->file('file'));

            return $this->successResponse('Successfully imported');
        } catch (Throwable $e) {
            $this->error($e);
            return $this->errorResponse(
                ResponseError::ERROR_508,
                'Excel format incorrect or data invalid'
            );
        }
    }
}
