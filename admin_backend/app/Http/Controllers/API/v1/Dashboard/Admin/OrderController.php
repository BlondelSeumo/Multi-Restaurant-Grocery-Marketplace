<?php

namespace App\Http\Controllers\API\v1\Dashboard\Admin;

use App\Exports\OrderExport;
use App\Helpers\ResponseError;
use App\Http\Requests\FilterParamsRequest;
use App\Http\Requests\Order\DeliveryManUpdateRequest;
use App\Http\Requests\Order\OrderChartPaginateRequest;
use App\Http\Requests\Order\OrderChartRequest;
use App\Http\Requests\Order\StocksCalculateRequest;
use App\Http\Requests\Order\StoreRequest;
use App\Http\Requests\Order\UpdateRequest;
use App\Http\Resources\OrderResource;
use App\Imports\OrderImport;
use App\Models\Order;
use App\Models\Settings;
use App\Models\Shop;
use App\Models\User;
use App\Repositories\DashboardRepository\DashboardRepository;
use App\Repositories\Interfaces\OrderRepoInterface;
use App\Repositories\OrderRepository\AdminOrderRepository;
use App\Services\Interfaces\OrderServiceInterface;
use App\Services\OrderService\OrderStatusUpdateService;
use App\Traits\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Maatwebsite\Excel\Facades\Excel;
use Throwable;

class OrderController extends AdminBaseController
{
    use Notification;

    /**
     * @param OrderRepoInterface $orderRepository
     * @param AdminOrderRepository $adminRepository
     * @param OrderServiceInterface $orderService
     */
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
     * @return AnonymousResourceCollection
     */
    public function index(): AnonymousResourceCollection
    {
        $orders = $this->orderRepository->ordersList();

        return OrderResource::collection($orders);
    }

    /**
     * Display a listing of the resource.
     *
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function paginate(FilterParamsRequest $request): JsonResponse
    {
        $filter = $request->all();

        $orders     = $this->adminRepository->ordersPaginate($filter);

        $filter['date_from'] = date('Y-m-d H:i:s', strtotime('-1 minute'));

        $statistic  = (new DashboardRepository)->orderByStatusStatistics($filter);
        $lastPage   = (new DashboardRepository)->getLastPage(
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
     * Display a listing of the resource.
     *
     * @param string $userId
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function userOrders(string $userId, FilterParamsRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = User::select(['id', 'uuid'])->where('uuid', $userId)->first();

        $filter = $request->merge(['user_id' => $user?->id])->all();

        $orders     = $this->adminRepository->userOrdersPaginate($filter);
        $statistic  = (new DashboardRepository)->orderByStatusStatistics($filter);
        $lastPage   = (new DashboardRepository)->getLastPage(data_get($filter, 'perPage', 10), $statistic,
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
     * Display a listing of the resource.
     *
     * @param string $userId
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function userOrder(string $userId, FilterParamsRequest $request): JsonResponse
    {
        $orderDetails = $this->adminRepository->userOrder($userId, $request->all());

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            $orderDetails
        );
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

        $firebaseToken = Shop::with(['seller'])->find(data_get($validated, 'shop_id'))?->seller?->firebase_token;

        $this->sendNotification(
            is_array($firebaseToken) ? $firebaseToken : [],
            "New order was created",
            data_get($result, 'data.id'),
            data_get($result, 'data')?->setAttribute('type', 'new_order')?->only(['id', 'status']),
        );

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_CREATED, locale: $this->language),
            $this->orderRepository->reDataOrder(data_get($result, 'data')),
        );
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show(int $id): JsonResponse
    {
        $order = $this->orderRepository->orderById($id);

        if (!$order) {
            return $this->onErrorResponse([
                'code' => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        return $this->successResponse(
            __('errors.' . ResponseError::SUCCESS, locale: $this->language),
            $this->orderRepository->reDataOrder($order)
        );
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
        $result = $this->orderService->update($id, $request->validated());

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
        $result = $this->orderRepository->orderStocksCalculate($request->validated());

        return $this->successResponse(__('errors.' . ResponseError::SUCCESS, locale: $this->language), $result);
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
        $result = $this->orderService->updateDeliveryMan($orderId, $request->input('deliveryman'));

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
     * @param int $id
     * @param FilterParamsRequest $request
     * @return JsonResponse
     */
    public function orderStatusUpdate(int $id, FilterParamsRequest $request): JsonResponse
    {
        /** @var Order $order */
        $order = Order::with([
            'shop.seller',
            'deliveryMan',
            'user.wallet',
        ])->find($id);

        if (!$order) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_404,
                'message'   => __('errors.' . ResponseError::ORDER_NOT_FOUND, locale: $this->language)
            ]);
        }

        if (!$order->user) {
            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_502,
                'message'   => __('errors.' . ResponseError::USER_NOT_FOUND, locale: $this->language)
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

    public function destroy(FilterParamsRequest $request): JsonResponse
    {
        $result = $this->orderService->destroy($request->input('ids'));

        if (count($result) > 0) {

            return $this->onErrorResponse([
                'code'      => ResponseError::ERROR_400,
                'message'   => __('errors.' . ResponseError::CANT_DELETE_ORDERS, [
                    'ids' => implode(', #', $result)
                ], locale: $this->language)
            ]);

        }

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function dropAll(): JsonResponse
    {
        $this->orderService->dropAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function truncate(): JsonResponse
    {
        $this->orderService->truncate();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function restoreAll(): JsonResponse
    {
        $this->orderService->restoreAll();

        return $this->successResponse(
            __('errors.' . ResponseError::RECORD_WAS_SUCCESSFULLY_DELETED, locale: $this->language)
        );
    }

    public function reportChart(OrderChartRequest $request): JsonResponse
    {
        try {
            $result = $this->orderRepository->ordersReportChart($request->validated());

            return $this->successResponse('Successfully imported', $result);
        } catch (Throwable $e) {

            $this->error($e);

            return $this->onErrorResponse(['code' => ResponseError::ERROR_400, 'message' => $e->getMessage()]);
        }
    }

    public function reportChartPaginate(OrderChartPaginateRequest $request): JsonResponse
    {
        try {
            $result = $this->orderRepository->ordersReportChartPaginate($request->validated());

            return $this->successResponse('Successfully data', $result);
        } catch (Throwable $e) {

            $this->error($e);

            return $this->onErrorResponse(['code' => ResponseError::ERROR_400, 'message' => $e->getMessage()]);
        }
    }

    public function revenueReport(OrderChartPaginateRequest $request): JsonResponse
    {
        try {
            $result = $this->orderRepository->revenueReport($request->validated());

            return $this->successResponse('Successfully data', $result);
        } catch (Throwable $e) {

            $this->error($e);

            return $this->onErrorResponse(['code' => ResponseError::ERROR_400, 'message' => $e->getMessage()]);
        }
    }

    public function overviewCarts(FilterParamsRequest $request): JsonResponse
    {
        try {
            $result = $this->orderRepository->overviewCarts($request->validated());

            return $this->successResponse('Successfully data', $result);
        } catch (Throwable $e) {

            $this->error($e);

            return $this->onErrorResponse(['code' => ResponseError::ERROR_400, 'message' => $e->getMessage()]);
        }
    }

    public function overviewProducts(OrderChartPaginateRequest $request): JsonResponse
    {
        try {
            $result = $this->orderRepository->overviewProducts($request->validated());

            return $this->successResponse('Successfully data', $result);
        } catch (Throwable $e) {

            $this->error($e);

            return $this->onErrorResponse(['code' => ResponseError::ERROR_400, 'message' => $e->getMessage()]);
        }
    }

    public function overviewCategories(OrderChartPaginateRequest $request): JsonResponse
    {
        try {
            $result = $this->orderRepository->overviewCategories($request->validated());

            return $this->successResponse('Successfully data', $result);
        } catch (Throwable $e) {

            $this->error($e);

            return $this->onErrorResponse(['code' => ResponseError::ERROR_400, 'message' => $e->getMessage()]);
        }
    }

    public function fileExport(FilterParamsRequest $request): JsonResponse
    {
        $fileName = 'export/orders.xls';

        try {
            $filter = $request->merge(['language' => $this->language])->all();

            Excel::store(new OrderExport($filter), $fileName, 'public');

            return $this->successResponse('Successfully exported', [
                'path'      => 'public/export',
                'file_name' => $fileName
            ]);
        } catch (Throwable $e) {
            $this->error($e);
            return $this->errorResponse(statusCode: ResponseError::ERROR_508, message: $e->getMessage());
        }
    }

    public function fileImport(Request $request): JsonResponse
    {
        try {

            Excel::import(new OrderImport($this->language), $request->file('file'));

            return $this->successResponse('Successfully imported');
        } catch (Throwable $e) {
            $this->error($e);
            return $this->errorResponse(statusCode: ResponseError::ERROR_508, message: $e->getMessage());
        }
    }
}
