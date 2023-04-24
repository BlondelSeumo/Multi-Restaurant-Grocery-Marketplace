<?php

namespace App\Http\Controllers\API\v1\Rest;

use App\Helpers\ResponseError;
use App\Helpers\Utility;
use App\Http\Requests\DeliveryZone\CheckDistanceRequest;
use App\Http\Requests\DeliveryZone\DistanceRequest;
use App\Models\DeliveryZone;
use App\Models\Shop;
use App\Traits\SetCurrency;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Throwable;

class DeliveryZoneController extends RestBaseController
{
    use SetCurrency;

    /**
     * @param int $shopId
     * @return array
     */
    public function getByShopId(int $shopId): array
    {
        try {
            $deliveryZone = DeliveryZone::where('shop_id', $shopId)->firstOrFail();

            return [
                'status' => true,
                'code'   => ResponseError::NO_ERROR,
                'data'   => $deliveryZone,
            ];
        } catch (Throwable $e) {
            $this->error($e);
            return [
                'status' => false,
                'code'   => ResponseError::ERROR_404,
            ];
        }
    }

    public function deliveryCalculatePrice(int $deliveryId, Request $request): float|JsonResponse
    {
        /** @var DeliveryZone $deliveryZone */
        $deliveryZone = DeliveryZone::find($deliveryId);

        if (!$deliveryZone) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::ERROR_404, locale: $this->language)
            ]);
        }

        $km = $request->input('km');

        if ($km <= 0) {
            $km = 1;
        }

        return round(
            ($deliveryZone->shop->price + ($deliveryZone->shop->price_per_km * $km)) * $this->currency(), 2
        );
    }

    /**
     * @param DistanceRequest $request
     * @return array
     */
    public function distance(DistanceRequest $request): array
    {
        return [
            'status' => true,
            'code'   => ResponseError::NO_ERROR,
            'data'   => (new Utility)->getDistance($request->input('origin'), $request->input('destination')),
        ];
    }

    /**
     * @param CheckDistanceRequest $request
     * @return JsonResponse
     */
    public function checkDistance(CheckDistanceRequest $request): JsonResponse
    {

        $shops = Shop::with('deliveryZone:id,shop_id,address')
            ->where([
                ['open', 1],
                ['status', 'approved'],
            ])
            ->whereHas('deliveryZone')
            ->select(['id', 'open', 'status'])
            ->get();

        foreach ($shops as $shop) {

            /** @var Shop $shop */
            $address = optional($shop->deliveryZone)->address;

            if (!is_array($address) || count($address) === 0) {
                continue;
            }

            $check = Utility::pointInPolygon($request->input('address'), $shop->deliveryZone->address);

            if ($check) {
                return $this->successResponse('success', 'success');
            }

        }

        return $this->onErrorResponse([
            'code'    => ResponseError::ERROR_400,
            'message' => __('errors.' . ResponseError::ERROR_400, locale: $this->language)
        ]);
    }

    /**
     * @param int $id
     * @param CheckDistanceRequest $request
     * @return JsonResponse
     */
    public function checkDistanceByShop(int $id, CheckDistanceRequest $request): JsonResponse
    {
        /** @var Shop $shop */
        $shop = Shop::with('deliveryZone:id,shop_id,address')->whereHas('deliveryZone')
            ->where([
                ['open', 1],
                ['status', 'approved'],
            ])
            ->select(['id', 'open', 'status'])
            ->find($id);

        if (empty($shop?->deliveryZone)) {
            return $this->onErrorResponse([
                'code'    => ResponseError::ERROR_404,
                'message' => __('errors.' . ResponseError::SHOP_OR_DELIVERY_ZONE, locale: $this->language)
            ]);
        }

        $check = Utility::pointInPolygon($request->input('address'), $shop->deliveryZone->address);

        if ($check) {
            return $this->successResponse('success');
        }

        return $this->onErrorResponse([
            'code'    => ResponseError::ERROR_400,
            'message' => ResponseError::NOT_IN_POLYGON
        ]);
    }

}
